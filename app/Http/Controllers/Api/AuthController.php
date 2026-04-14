<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\EmailSequence;
use App\Models\User;
use App\Services\MetaConversionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'id' => Str::uuid(),
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'] ?? null,
                'role' => 'student',
                'email_verified' => true,
            ]);

            // Create or get API token
            if (!$user->api_token) {
                $user->api_token = \Str::random(60);
            }
            // Set token expiration to 30 days from now
            $user->token_expires_at = now()->addDays(30);
            // Set onboarding_step to 1 (welcome email will be sent)
            $user->onboarding_step = 1;
            // Set initial activity tracking
            $user->last_active_at = now();
            $user->save();

            // Create session token with API token
            $token = base64_encode($user->id . '|' . $user->api_token);

            // Track registration with Meta Conversions API
            try {
                $metaConversions = app(MetaConversionsService::class);
                $metaConversions->trackRegistration($user, $request, $request->input('meta_event_id'), 'email');
            } catch (\Exception $e) {
                Log::warning('Meta CAPI: CompleteRegistration failed', ['error' => $e->getMessage()]);
            }

            // Send welcome email (wrapped in try-catch to prevent failures)
            try {
                Mail::to($user->email)->send(new WelcomeMail($user));
            } catch (\Exception $e) {
                Log::warning('Failed to send welcome email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                // Don't fail registration if email fails
            }

            // Auto-subscribe to default drip email sequence
            try {
                $defaultSequence = EmailSequence::getDefault();
                if ($defaultSequence) {
                    $defaultSequence->subscribe($user);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to subscribe user to drip sequence', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'onboarding_completed' => (bool) $user->onboarding_completed,
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
            Log::error('Registration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'การลงทะเบียนล้มเหลว กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();
            
            // Debug logging
            \Log::info('Login attempt', [
                'email' => $validated['email'],
                'user_found' => $user ? true : false,
                'password_provided' => $validated['password'] ? 'yes' : 'no'
            ]);

            if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
                \Log::warning('Login failed', [
                    'email' => $validated['email'],
                    'user_exists' => $user ? true : false,
                    'password_check' => $user ? Hash::check($validated['password'], $user->password_hash) : false
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            // Create or get API token
            if (!$user->api_token) {
                $user->api_token = \Str::random(60);
            }
            // Set token expiration to 30 days from now
            $user->token_expires_at = now()->addDays(30);
            // Update activity tracking
            $user->last_active_at = now();
            // Save token fields before updateStreak
            $user->save();
            $user->updateStreak();

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
                    'onboarding_completed' => (bool) $user->onboarding_completed,
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
            Log::error('Login failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'เข้าสู่ระบบล้มเหลว กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            // Get user from middleware (Auth::user() or $request->auth_user)
            $user = Auth::user() ?? $request->auth_user;

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
                    'onboarding_completed' => (bool) $user->onboarding_completed,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Profile fetch failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลโปรไฟล์ได้ กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user() ?? $request->auth_user;
        if ($user) {
            $user->api_token = null;
            $user->token_expires_at = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function submitOnboarding(Request $request)
    {
        try {
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $validated = $request->validate([
                'goals' => 'required|array|min:1',
                'goals.*' => 'string',
                'interests' => 'required|array|min:1',
                'interests.*' => 'string',
                'experience_level' => 'required|string|in:beginner,intermediate,advanced',
            ]);

            $user->goals = $validated['goals'];
            $user->interests = $validated['interests'];
            $user->experience_level = $validated['experience_level'];
            $user->onboarding_completed = true;
            $user->onboarding_step = 5;
            $user->save();

            // Get recommended courses based on interests
            $recommendedCourses = $this->getRecommendedCourses($user);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding completed',
                'data' => [
                    'goals' => $user->goals,
                    'interests' => $user->interests,
                    'experience_level' => $user->experience_level,
                    'recommended_courses' => $recommendedCourses,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Onboarding failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    public function getOnboardingCourses(Request $request)
    {
        try {
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            $courses = $this->getRecommendedCourses($user);

            return response()->json([
                'success' => true,
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get onboarding courses', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลคอร์สได้',
            ], 500);
        }
    }

    private function getRecommendedCourses(User $user): array
    {
        $query = \App\Models\Course::where('is_published', true)
            ->with('category');

        // If user has interests, prioritize matching categories
        if (!empty($user->interests)) {
            $categoryIds = \App\Models\Category::whereIn('slug', $user->interests)
                ->pluck('id')
                ->toArray();

            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by experience level
        if ($user->experience_level === 'beginner') {
            $query->orderByRaw("CASE WHEN level = 'beginner' THEN 0 WHEN level = 'intermediate' THEN 1 ELSE 2 END");
        } elseif ($user->experience_level === 'advanced') {
            $query->orderByRaw("CASE WHEN level = 'advanced' THEN 0 WHEN level = 'intermediate' THEN 1 ELSE 2 END");
        }

        return $query->take(6)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'level' => $course->level,
                    'price' => $course->price,
                    'thumbnail_url' => $course->thumbnail_url,
                    'category' => $course->category ? $course->category->name : null,
                    'category_slug' => $course->category ? $course->category->slug : null,
                    'total_lessons' => $course->total_lessons,
                ];
            })
            ->toArray();
    }

    public function googleLogin(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'id_token' => 'required|string',
            ]);

            // Verify the Google ID token
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google token',
                ], 401);
            }

            // Extract user info from Google payload
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'] ?? null;
            $avatar = $payload['picture'] ?? null;
            $emailVerified = $payload['email_verified'] ?? false;

            // Find or create user
            // First try to find by email (existing user linking Google account)
            $user = User::where('email', $email)->first();

            if (!$user) {
                // Create new user (no password needed for Google auth)
                $user = User::create([
                    'id' => Str::uuid(),
                    'email' => $email,
                    'password_hash' => Hash::make(Str::random(32)), // random password since they use Google
                    'full_name' => $name,
                    'avatar_url' => $avatar,
                    'role' => 'student',
                    'email_verified' => $emailVerified,
                    'google_id' => $googleId,
                    'onboarding_step' => 1, // welcome email will be sent
                    'last_active_at' => now(),
                ]);
            } else {
                // Update existing user with Google info if not set
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                }
                if (!$user->avatar_url && $avatar) {
                    $user->avatar_url = $avatar;
                }
                if (!$user->email_verified && $emailVerified) {
                    $user->email_verified = $emailVerified;
                }
                // Update activity tracking for existing users
                $user->last_active_at = now();
                $user->updateStreak();
                // Note: updateStreak() already calls save(), so we don't need to call it again
            }

            // Generate API token (same as regular login)
            if (!$user->api_token) {
                $user->api_token = Str::random(60);
            }
            $user->token_expires_at = now()->addDays(30);
            $user->save();

            $token = base64_encode($user->id . '|' . $user->api_token);

            // Send welcome email for new users (if just created)
            if ($user->wasRecentlyCreated) {
                try {
                    Mail::to($user->email)->send(new WelcomeMail($user));
                } catch (\Exception $e) {
                    Log::warning('Failed to send welcome email for Google user', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                }

                // Auto-subscribe to default drip email sequence
                try {
                    $defaultSequence = EmailSequence::getDefault();
                    if ($defaultSequence) {
                        $defaultSequence->subscribe($user);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to subscribe Google user to drip sequence', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Track registration with Meta Conversions API (new Google users only)
                try {
                    $metaConversions = app(MetaConversionsService::class);
                    $metaConversions->trackRegistration($user, $request, $request->input('meta_event_id'), 'google');
                } catch (\Exception $e) {
                    Log::warning('Meta CAPI: Google CompleteRegistration failed', ['error' => $e->getMessage()]);
                }
            }

            // Return same format as regular login
            return response()->json([
                'success' => true,
                'message' => 'Google login successful',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'role' => $user->role,
                    'avatar_url' => $user->avatar_url,
                    'onboarding_completed' => (bool) $user->onboarding_completed,
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
            Log::error('Google login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Google login failed. Please try again.',
            ], 500);
        }
    }
}