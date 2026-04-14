<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailSequenceSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailSubscriptionController extends Controller
{
    /**
     * Unsubscribe a user from a drip email sequence.
     *
     * Token format: base64(user_id|sequence_id)
     * Accessible without authentication (clicked from email).
     */
    public function unsubscribe(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return $this->showUnsubscribePage(false, 'ลิงก์ไม่ถูกต้อง');
        }

        $decoded = base64_decode($token, true);

        if (!$decoded || !str_contains($decoded, '|')) {
            return $this->showUnsubscribePage(false, 'ลิงก์ไม่ถูกต้อง');
        }

        [$userId, $sequenceId] = explode('|', $decoded, 2);

        $subscription = EmailSequenceSubscription::where('user_id', $userId)
            ->where('sequence_id', $sequenceId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return $this->showUnsubscribePage(false, 'คุณยกเลิกการรับอีเมลนี้ไปแล้ว หรือลิงก์ไม่ถูกต้อง');
        }

        $subscription->unsubscribe();

        Log::info('User unsubscribed from drip sequence', [
            'user_id' => $userId,
            'sequence_id' => $sequenceId,
        ]);

        return $this->showUnsubscribePage(true, 'ยกเลิกการรับอีเมลซีรีส์นี้เรียบร้อยแล้ว');
    }

    /**
     * Render a simple unsubscribe confirmation page.
     */
    private function showUnsubscribePage(bool $success, string $message)
    {
        $frontendUrl = config('app.frontend_url', 'https://brieflylearn.com');

        return response()->view('emails.unsubscribe', [
            'success' => $success,
            'message' => $message,
            'frontendUrl' => $frontendUrl,
        ]);
    }
}
