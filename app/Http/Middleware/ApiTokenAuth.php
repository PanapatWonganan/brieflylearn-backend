<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract Authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return $this->unauthorizedResponse('No authorization token provided');
        }

        // Remove "Bearer " prefix if present
        $token = str_replace('Bearer ', '', $authHeader);

        // Decode the base64 token
        $decoded = base64_decode($token, true);

        // Check if decode was successful
        if ($decoded === false) {
            return $this->unauthorizedResponse('Invalid token format');
        }

        // Split by pipe separator
        $parts = explode('|', $decoded);

        // Validate token format: should be userId|tokenHash
        if (count($parts) !== 2) {
            return $this->unauthorizedResponse('Malformed token structure');
        }

        [$userId, $tokenHash] = $parts;

        // Find user by ID
        $user = User::find($userId);

        if (!$user) {
            return $this->unauthorizedResponse('User not found');
        }

        // Verify token matches the stored api_token
        if ($user->api_token !== $tokenHash) {
            return $this->unauthorizedResponse('Invalid token');
        }

        // Check token expiration if token_expires_at exists
        if (isset($user->token_expires_at) && $user->token_expires_at) {
            if (now()->isAfter($user->token_expires_at)) {
                return $this->unauthorizedResponse('Token has expired');
            }
        }

        // Set the authenticated user on the request
        $request->merge(['auth_user' => $user]);

        // Also set user in Laravel's Auth system
        Auth::setUser($user);

        // Update last active timestamp and streak (throttle to once per 5 minutes)
        if (!$user->last_active_at || $user->last_active_at->diffInMinutes(now()) >= 5) {
            $user->last_active_at = now();
            $user->updateStreak();
            // Note: updateStreak() already calls save(), so we don't need to call it again here
        }

        return $next($request);
    }

    /**
     * Return a standardized unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }
}
