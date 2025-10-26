<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            $user = User::create([
                'id' => Str::uuid(),
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'role' => 'student',
                'email_verified' => true,
            ]);

            // Create or get API token
            if (!$user->api_token) {
                $user->api_token = \Str::random(60);
                $user->save();
            }
            // Create session token with API token
            $token = base64_encode($user->id . '|' . $user->api_token);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();
            
            // Debug logging
            \Log::info('Login attempt', [
                'email' => $request->email,
                'user_found' => $user ? true : false,
                'password_provided' => $request->password ? 'yes' : 'no'
            ]);

            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                \Log::warning('Login failed', [
                    'email' => $request->email,
                    'user_exists' => $user ? true : false,
                    'password_check' => $user ? Hash::check($request->password, $user->password_hash) : false
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Create or get API token
            if (!$user->api_token) {
                $user->api_token = \Str::random(60);
                $user->save();
            }
            // Create session token with API token
            $token = base64_encode($user->id . '|' . $user->api_token);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                ],
                'token' => $token,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided',
                ], 401);
            }

            // Remove "Bearer " prefix if present
            $token = str_replace('Bearer ', '', $token);
            
            // Decode token
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) !== 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format',
                ], 401);
            }

            $userId = $parts[0];
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'avatar_url' => $user->avatar_url,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile fetch failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}