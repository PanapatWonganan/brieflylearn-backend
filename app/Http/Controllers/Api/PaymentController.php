<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentInitiatedMail;
use App\Mail\PaymentSuccessMail;
use App\Models\Course;
use App\Models\Enrollment;
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
    public function __construct(protected PaysolutionsService $pay)
    {
    }

    /**
     * Create (or reuse) a pending enrollment and return the fields the
     * frontend must POST to the Pay Solutions hosted page.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
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

        // Free course → auto-enroll, no gateway.
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
                'redirect_url' => config('app.frontend_url', 'https://brieflylearn.com')
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
            $enrollment->order_no = $enrollment->order_no ?: $this->pay->generateOrderNo();
            $enrollment->save();
        }

        $checkout = $this->pay->buildCheckout($enrollment);

        // Fire-and-forget "awaiting payment" email
        try {
            Mail::to($user->email)->send(new PaymentInitiatedMail($enrollment));
        } catch (\Throwable $e) {
            Log::warning('PaymentInitiatedMail failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'order_no' => $checkout['order_no'],
            'url' => $checkout['url'],
            'fields' => $checkout['fields'],
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
        $orderNo = (string) $request->query('refno', $request->query('orderNo', ''));
        $frontend = rtrim((string) config('app.frontend_url', 'https://brieflylearn.com'), '/');

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
        DB::transaction(function () use ($orderNo, $isSuccess, $payload) {
            $enrollment = Enrollment::where('order_no', $orderNo)->lockForUpdate()->first();
            if (! $enrollment) {
                Log::warning('Paysolutions: enrollment not found for order_no', ['order_no' => $orderNo]);
                return;
            }

            // Idempotent: ignore duplicate success postbacks.
            if ($enrollment->payment_status === 'completed' && $isSuccess) {
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
            } else {
                $enrollment->status = 'pending';
                $enrollment->payment_status = 'failed';
            }

            $enrollment->save();

            $enrollment->loadMissing(['user', 'course']);

            try {
                if ($isSuccess) {
                    Mail::to($enrollment->user->email)->send(new PaymentSuccessMail($enrollment));
                } else {
                    $reason = (string) ($payload['message'] ?? $payload['responseMessage'] ?? '');
                    Mail::to($enrollment->user->email)->send(new PaymentFailedMail($enrollment, $reason ?: null));
                }
            } catch (\Throwable $e) {
                Log::warning('Payment result email failed', [
                    'order_no' => $orderNo,
                    'success' => $isSuccess,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
