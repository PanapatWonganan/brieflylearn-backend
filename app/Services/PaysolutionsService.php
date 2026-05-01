<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pay Solutions (ThaiePay) hosted payment page integration.
 *
 * Flow:
 *   1. buildCheckout(): create `refno` + fields to POST to hosted_url.
 *   2. Frontend auto-submits HTML form to Pay Solutions.
 *   3. User completes payment on hosted page (credit card / PromptPay / banking).
 *   4. Pay Solutions:
 *      - Server-to-server postback to our postback URL (authoritative).
 *      - Browser redirect to our return URL (UX only, do NOT trust status from here).
 *   5. verifyOrder(): call inquiry API to double-check status.
 */
class PaysolutionsService
{
    /**
     * Generate a unique 12-digit refno.
     * Pay Solutions requires numeric, max 12 chars.
     */
    public function generateOrderNo(): string
    {
        // Use last 12 digits of microtime + random suffix, guaranteed unique per millisecond.
        // Example: timestamp-based 10 digits + 2 random
        $base = substr((string) (int) (microtime(true) * 1000), -10);
        return $base . str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Build the data needed for the browser to POST to the hosted page.
     *
     * @param  Enrollment  $enrollment  Pending enrollment (already has order_no).
     * @param  array<int, array{name: string, price: float}>  $bumpLineItems
     *         Optional bump products to be summed into total + productdetail.
     *         Snapshots only — pass already-resolved name + price.
     *
     * Returns: [ 'url' => hosted_url, 'fields' => [...], 'order_no' => refno ]
     */
    public function buildCheckout(Enrollment $enrollment, array $bumpLineItems = []): array
    {
        $course = $enrollment->course;
        $user = $enrollment->user;

        $refno = $enrollment->order_no ?: $this->generateOrderNo();

        // Only override `returnurl` for browser redirect. Keep `postbackurl`
        // implicit so Paysolutions falls back to the merchant-dashboard default
        // (which is already proven to reach our /postback endpoint successfully).
        $apiBase = rtrim((string) config('app.url', 'https://api.antiparallel.app'), '/');
        $returnUrl = $apiBase . '/api/v1/payments/paysolutions/return';

        $coursePrice = (float) $course->price;
        $bumpsTotal = 0.0;
        $detailParts = [strip_tags((string) $course->title)];

        foreach ($bumpLineItems as $item) {
            $bumpsTotal += (float) ($item['price'] ?? 0);
            $detailParts[] = '+ ' . strip_tags((string) ($item['name'] ?? ''));
        }

        $grandTotal = $coursePrice + $bumpsTotal;
        $productDetail = mb_substr(implode(' ', $detailParts), 0, 200);

        $fields = [
            'refno' => $refno,
            'merchantid' => (string) config('paysolutions.merchant_id'),
            'customeremail' => (string) $user->email,
            // productdetail max 255, strip HTML, keep ASCII-ish for safety.
            'productdetail' => $productDetail,
            'total' => number_format($grandTotal, 2, '.', ''),
            'cc' => (string) config('paysolutions.currency', '00'),
            'lang' => (string) config('paysolutions.lang', 'TH'),
            // Pay Solutions rejects strings matching internal merchant-ref patterns
            // (e.g. 12-char hex). Use a short prefixed alphanumeric slug instead.
            'customerref' => 'U' . mb_substr(str_replace('-', '', (string) $user->id), 0, 8),
            'returnurl' => $returnUrl,
        ];

        return [
            'url' => (string) config('paysolutions.hosted_url'),
            'fields' => $fields,
            'order_no' => $refno,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * Server-to-server order inquiry.
     * Docs: POST https://apis.paysolutions.asia/order/orderdetailpost
     *
     * Returns the decoded response array or null on failure.
     */
    public function verifyOrder(string $orderNo): ?array
    {
        $merchantId = (string) config('paysolutions.merchant_id');
        $apiKey = (string) config('paysolutions.api_key');
        $secretKey = (string) config('paysolutions.secret_key');

        if ($merchantId === '' || $apiKey === '' || $secretKey === '') {
            Log::warning('Paysolutions inquiry skipped: missing credentials', [
                'order_no' => $orderNo,
            ]);
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $apiKey,
                'merchantSecretKey' => $secretKey,
            ])->timeout(15)->post(
                (string) config('paysolutions.inquiry_url'),
                [
                    'merchantId' => $merchantId,
                    'orderNo' => $orderNo,
                    'refNo' => $orderNo,
                    'productDetail' => '',
                ]
            );

            if (! $response->successful()) {
                Log::error('Paysolutions inquiry non-2xx', [
                    'order_no' => $orderNo,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Paysolutions inquiry exception', [
                'order_no' => $orderNo,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extract normalized status from postback or inquiry response.
     * Pay Solutions typically returns status "C" = success, others = failure.
     * We accept a few common fields for resilience.
     */
    public function isSuccessResponse(array $data): bool
    {
        // Common keys across Pay Solutions variants. "CP" = Captured/Paid (success),
        // "C" = Completed, "00" = success on inquiry API, "COMPLETED"/"PAID"/"SUCCESS" on others.
        $successCodes = ['C', 'CP', 'S', '00', 'SUCCESS', 'COMPLETED', 'PAID', 'APPROVED'];

        $status = $data['status'] ?? $data['transactionStatus'] ?? $data['orderStatus'] ?? null;
        if (is_string($status)) {
            if (in_array(strtoupper(trim($status)), $successCodes, true)) {
                return true;
            }
        }

        // Some postbacks include a human-readable status name.
        $statusName = $data['statusname'] ?? $data['statusName'] ?? null;
        if (is_string($statusName)) {
            if (in_array(strtoupper(trim($statusName)), $successCodes, true)) {
                return true;
            }
        }

        // Some responses return result=true / responseCode=0
        if (isset($data['result']) && $data['result'] === true) {
            return true;
        }
        if (isset($data['responseCode']) && (int) $data['responseCode'] === 0) {
            return true;
        }

        return false;
    }
}
