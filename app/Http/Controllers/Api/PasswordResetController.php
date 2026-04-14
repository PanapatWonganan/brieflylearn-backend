<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link to user's email
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้งานที่มีอีเมลนี้',
                ], 404);
            }

            // Delete any existing tokens for this email
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Generate a new token
            $token = Str::random(64);

            // Store the token in database
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // Create reset URL with token
            $frontendUrl = config('app.frontend_url', 'https://brieflylearn.com');
            $resetUrl = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);

            // Send email with try-catch to prevent failure from breaking the flow
            try {
                Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
            } catch (\Exception $e) {
                Log::warning('Failed to send password reset email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถส่งอีเมลได้ กรุณาลองอีกครั้งในภายหลัง',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณเรียบร้อยแล้ว',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password reset request failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองอีกครั้ง',
            ], 500);
        }
    }

    /**
     * Reset user's password using token
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|min:6|confirmed',
            ]);

            // Find the token record
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$tokenRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว',
                ], 400);
            }

            // Check if token is expired (60 minutes)
            if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
                // Delete expired token
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'ลิงก์รีเซ็ตรหัสผ่านหมดอายุแล้ว กรุณาขอลิงก์ใหม่',
                ], 400);
            }

            // Verify token
            if (!Hash::check($request->token, $tokenRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้อง',
                ], 400);
            }

            // Find user
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบผู้ใช้งาน',
                ], 404);
            }

            // Update password
            $user->password_hash = Hash::make($request->password);
            $user->save();

            // Delete the token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านสำเร็จ คุณสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้แล้ว',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด กรุณาลองอีกครั้ง',
            ], 500);
        }
    }
}
