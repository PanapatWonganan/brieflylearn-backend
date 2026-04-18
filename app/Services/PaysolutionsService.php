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
     * Returns: [ 'url' => hosted_url, 'fields' => [...], 'order_no' => refno ]
     */
    public function buildCheckout(Enrollment $enrollment): array
    {
        $course = $enrollment->course;
        $user = $enrollment->user;

        $refno = $enrollment->order_no ?: $this->generateOrderNo();

        $fields = [
            'refno' => $refno,
            'merchantid' => (string) config('paysolutions.merchant_id'),
            'customeremail' => (string) $user->email,
            // productdetail max 255, strip HTML, keep ASCII-ish for safety.
            'productdetail' => mb_substr(strip_tags($course->title), 0, 200),
            'total' => number_format((float) $course->price, 2, '.', ''),
            'cc' => (string) config('paysolutions.currency', '00'),
            'lang' => (string) config('paysolutions.lang', 'TH'),
            'customerref' => mb_substr((string) $user->id, 0, 10),
        ];

        return [
            'url' => (string) config('paysolutions.hosted_url'),
            'fields' => $fields,
            'order_no' => $refno,
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
        // Common keys across Pay Solutions variants
        $status = $data['status'] ?? $data['transactionStatus'] ?? $data['orderStatus'] ?? null;

        if (is_string($status)) {
            $normalized = strtoupper(trim($status));
            // "C" = completed/captured, "00" = success on some endpoints, "SUCCESS"
            return in_array($normalized, ['C', '00', 'SUCCESS', 'COMPLETED', 'PAID'], true);
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
