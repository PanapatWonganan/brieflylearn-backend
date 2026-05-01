<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentInitiatedMail;
use App\Mail\PaymentSuccessMail;
use App\Models\BumpProduct;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Services\OrderItemFulfillmentService;
use App\Services\PaysolutionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Handles the Pay Solutions (ThaiePay) payment flow.
 *
 * Endpoints:
 *   POST /v1/payments/paysolutions/checkout  (auth): build checkout fields
 *   POST /v1/payments/paysolutions/postback  (public): server-to-server confirmation
 *   GET  /v1/payments/paysolutions/return    (public): browser return (UX only)
 *   GET  /v1/payments/paysolutions/status    (auth):  poll latest status by order_no
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaysolutionsService $pay,
        protected OrderItemFulfillmentService $fulfillment,
    ) {
    }

    /**
     * Create (or reuse) a pending enrollment and return the fields the
     * frontend must POST to the Pay Solutions hosted page.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
            'bump_slugs' => ['sometimes', 'array', 'max:10'],
            'bump_slugs.*' => ['string', 'max:80'],
        ]);

        $user = Auth::user() ?? $request->auth_user;
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'ยังไม่ได้เข้าสู่ระบบ'], 401);
        }

        $course = Course::findOrFail($validated['course_id']);

        // Already paid? Short-circuit.
        $paid = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('payment_status', 'completed')
            ->first();
        if ($paid) {
            return response()->json([
                'success' => true,
                'already_paid' => true,
                'message' => 'คุณได้ลงทะเบียนคอร์สนี้แล้ว',
                'enrollment' => $paid,
            ]);
        }

        // Resolve requested bumps to live BumpProduct rows. Silently drop any
        // slug that is unknown or inactive — never charge a stale slug.
        $requestedSlugs = array_values(array_unique($validated['bump_slugs'] ?? []));
        $bumps = collect();
        if (! empty($requestedSlugs)) {
            $bumps = BumpProduct::query()
                ->active()
                ->forCourse($course->id)
                ->whereIn('slug', $requestedSlugs)
                ->get();
        }

        // Free course → auto-enroll, no gateway. Bumps on a free course are not
        // supported (no money flow to attach them to); ignore them here.
        if ((float) $course->price <= 0) {
            $enrollment = Enrollment::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_status' => 'completed',
                    'amount_paid' => 0,
                    'payment_date' => now(),
                    'payment_method' => 'free',
                    'payment_gateway' => 'free',
                ]
            );
            return response()->json([
                'success' => true,
                'free' => true,
                'enrollment' => $enrollment,
                'redirect_url' => config('app.frontend_url', 'https://antiparallel.app')
                    . '/courses/' . $course->id,
            ]);
        }

        // Reuse an existing pending enrollment (same order_no) or create new.
        $enrollment = Enrollment::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        if (! $enrollment->exists || $enrollment->payment_status !== 'completed') {
            $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
            $enrollment->status = 'pending';
            $enrollment->payment_status = 'pending';
            $enrollment->payment_gateway = 'paysolutions';
            $enrollment->payment_method = 'paysolutions';
            // Always regenerate order_no on a new checkout attempt — Pay Solutions
            // rejects any refno that has been previously sent to the hosted page.
            $enrollment->order_no = $this->pay->generateOrderNo();
            $enrollment->save();
        }

        // Snapshot bumps as OrderItems (idempotent: re-checkout replaces
        // the not-yet-delivered items with the latest selection).
        DB::transaction(function () use ($enrollment, $bumps) {
            // Drop any prior undelivered items so the user can change their mind
            // before paying. Delivered items stay (they're history).
            OrderItem::where('enrollment_id', $enrollment->id)
                ->whereNull('delivered_at')
                ->delete();

            foreach ($bumps as $bump) {
                OrderItem::create([
                    'enrollment_id' => $enrollment->id,
                    'bump_product_id' => $bump->id,
                    'name_snapshot' => $bump->name,
                    'price_snapshot' => $bump->price,
                ]);
            }
        });

        $bumpLineItems = $bumps->map(fn (BumpProduct $b) => [
            'name' => $b->name,
            'price' => (float) $b->price,
        ])->values()->all();

        $checkout = $this->pay->buildCheckout($enrollment, $bumpLineItems);

        // Fire-and-forget "awaiting payment" email
        try {
            Mail::to($user->email)->queue(new PaymentInitiatedMail($enrollment));
        } catch (\Throwable $e) {
            Log::warning('PaymentInitiatedMail failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'order_no' => $checkout['order_no'],
            'url' => $checkout['url'],
            'fields' => $checkout['fields'],
            'grand_total' => $checkout['grand_total'] ?? null,
            'bumps' => $bumpLineItems,
        ]);
    }

    /**
     * Server-to-server postback from Pay Solutions.
     * Authoritative status — always verify via inquiry API before trusting.
     * Must return 200 quickly; do NOT require auth.
     */
    public function postback(Request $request)
    {
        $payload = $request->all();
        $orderNo = (string) ($payload['refno'] ?? $payload['orderNo'] ?? $payload['order_no'] ?? '');

        Log::info('Paysolutions postback', ['order_no' => $orderNo, 'payload' => $payload]);

        if ($orderNo === '') {
            return response()->json(['success' => false, 'message' => 'missing refno'], 200);
        }

        // Verify via inquiry API (do not trust the postback body alone).
        $inquiry = $this->pay->verifyOrder($orderNo);
        $verified = is_array($inquiry) ? $inquiry : $payload;
        $isSuccess = $this->pay->isSuccessResponse($verified);

        $this->applyPaymentResult($orderNo, $isSuccess, $verified);

        return response()->json(['success' => true], 200);
    }

    /**
     * Browser return URL — user comes back here after the hosted page.
     * Status from query string is NOT trusted; we re-verify via inquiry API.
     *
     * Redirects the browser to the frontend success / failed page.
     */
    public function return(Request $request)
    {
        // Paysolutions may return via GET (query string) OR POST (form body),
        // and the key name varies (refno / orderNo / order_no). Read from both.
        $orderNo = (string) (
            $request->input('refno')
                ?? $request->input('orderNo')
                ?? $request->input('order_no')
                ?? ''
        );
        $frontend = rtrim((string) config('app.frontend_url', 'https://antiparallel.app'), '/');

        if ($orderNo === '') {
            return redirect($frontend . '/payments/failed?reason=missing_order');
        }

        $inquiry = $this->pay->verifyOrder($orderNo);
        $verified = is_array($inquiry) ? $inquiry : $request->all();
        $isSuccess = $this->pay->isSuccessResponse($verified);

        $this->applyPaymentResult($orderNo, $isSuccess, $verified);

        $target = $isSuccess
            ? $frontend . '/payments/success?order_no=' . urlencode($orderNo)
            : $frontend . '/payments/failed?order_no=' . urlencode($orderNo);

        return redirect($target);
    }

    /**
     * Authenticated status lookup — frontend polls after returning.
     */
    public function status(Request $request)
    {
        $user = Auth::user() ?? $request->auth_user;
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'ยังไม่ได้เข้าสู่ระบบ'], 401);
        }

        $orderNo = (string) $request->query('order_no', '');
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('order_no', $orderNo)
            ->with('course:id,title,price')
            ->first();

        if (! $enrollment) {
            return response()->json(['success' => false, 'message' => 'ไม่พบคำสั่งซื้อ'], 404);
        }

        return response()->json([
            'success' => true,
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Apply the verified payment status to the matching Enrollment and
     * send the appropriate email.
     */
    protected function applyPaymentResult(string $orderNo, bool $isSuccess, array $payload): void
    {
        // Track whether this call is the one that flips the enrollment to
        // completed. If so, fulfil bumps AFTER the transaction commits
        // (so OrderItems written in the same tx are visible to fulfillment).
        $shouldFulfilEnrollmentId = null;

        DB::transaction(function () use ($orderNo, $isSuccess, $payload, &$shouldFulfilEnrollmentId) {
            $enrollment = Enrollment::where('order_no', $orderNo)->lockForUpdate()->first();
            if (! $enrollment) {
                Log::warning('Paysolutions: enrollment not found for order_no', ['order_no' => $orderNo]);
                return;
            }

            // Idempotent: ignore duplicate success postbacks.
            if ($enrollment->payment_status === 'completed' && $isSuccess) {
                return;
            }

            // Race-condition guard: once an enrollment has been marked
            // completed (typically via /postback which carries the authoritative
            // status=CP body from Paysolutions), a later /return call with an
            // ambiguous/empty body must NEVER downgrade it back to failed.
            // This protects users from losing access if the return redirect
            // lands after the postback and the inquiry API is flaky.
            if ($enrollment->payment_status === 'completed' && ! $isSuccess) {
                Log::info('Paysolutions: ignoring late non-success signal for already-completed enrollment', [
                    'order_no' => $orderNo,
                    'enrollment_id' => $enrollment->id,
                ]);
                return;
            }

            $enrollment->gateway_response = $payload;
            $enrollment->transaction_id = (string) ($payload['tranRef']
                ?? $payload['transactionId']
                ?? $payload['txnRef']
                ?? $enrollment->transaction_id
                ?? '');

            if ($isSuccess) {
                $enrollment->status = 'active';
                $enrollment->payment_status = 'completed';
                $enrollment->payment_date = now();
                $enrollment->amount_paid = (float) ($payload['total']
                    ?? $payload['amount']
                    ?? $enrollment->course?->price
                    ?? 0);
                $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
                $shouldFulfilEnrollmentId = $enrollment->id;
            } else {
                $enrollment->status = 'pending';
                $enrollment->payment_status = 'failed';
            }

            $enrollment->save();

            $enrollment->loadMissing(['user', 'course']);

            try {
                if ($isSuccess) {
                    Mail::to($enrollment->user->email)->queue(new PaymentSuccessMail($enrollment));
                } else {
                    $reason = (string) ($payload['message'] ?? $payload['responseMessage'] ?? '');
                    Mail::to($enrollment->user->email)->queue(new PaymentFailedMail($enrollment, $reason ?: null));
                }
            } catch (\Throwable $e) {
                Log::warning('Payment result email failed', [
                    'order_no' => $orderNo,
                    'success' => $isSuccess,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        // Bump fulfillment runs OUTSIDE the parent transaction so failures
        // here never roll back the (already-confirmed) payment + enrollment.
        if ($shouldFulfilEnrollmentId !== null) {
            try {
                $enrollment = Enrollment::find($shouldFulfilEnrollmentId);
                if ($enrollment) {
                    $this->fulfillment->fulfilForEnrollment($enrollment);
                }
            } catch (\Throwable $e) {
                Log::error('Bump fulfillment dispatch failed', [
                    'order_no' => $orderNo,
                    'enrollment_id' => $shouldFulfilEnrollmentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
