<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Playbook API — thin wrapper around Course records where content_type = 'playbook'.
 *
 * Playbooks reuse the Course + Enrollment + Payment infrastructure. The only
 * difference is the content type: a single lesson whose html_content is served
 * to users who have paid (and withheld from everyone else).
 */
class PlaybookController extends Controller
{
    /**
     * List all published playbooks. Public endpoint, no gating.
     */
    public function index(Request $request)
    {
        $playbooks = Course::where('content_type', 'playbook')
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail_url' => $course->thumbnail_url,
                    'price' => $course->price,
                    'original_price' => $course->original_price,
                    'rating' => $course->rating,
                    'total_students' => $course->total_students,
                    'content_type' => $course->content_type,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $playbooks,
        ]);
    }

    /**
     * Show a single playbook. Includes html_content only when the caller has
     * a completed enrollment for this playbook (or the playbook is free).
     *
     * This endpoint is public but honors an optional Bearer token to surface
     * purchase state — same pattern as LessonController::getCourseLessons.
     */
    public function show(Request $request, string $id)
    {
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('order_index', 'asc');
        }])
            ->where('content_type', 'playbook')
            ->findOrFail($id);

        $user = $this->getAuthenticatedUser($request) ?: $this->resolveBearerUser($request);

        $userHasPaidAccess = false;
        if ($user) {
            $enrollment = $user->enrollments()
                ->where('course_id', $course->id)
                ->orderByDesc('enrolled_at')
                ->first();

            if ($enrollment) {
                $coursePrice = (float) ($course->price ?? 0);
                $userHasPaidAccess = $coursePrice <= 0
                    ? true
                    : $enrollment->payment_status === 'completed';
            }
        }

        // Playbook should have exactly one lesson in MVP, but tolerate more.
        $lesson = $course->lessons->first();
        $htmlContent = null;
        if ($lesson && $userHasPaidAccess) {
            $htmlContent = $lesson->html_content;
        }

        $response = response()->json([
            'success' => true,
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail_url' => $course->thumbnail_url,
                'price' => $course->price,
                'original_price' => $course->original_price,
                'content_type' => $course->content_type,
                'user_has_paid_access' => $userHasPaidAccess,
                'lesson' => $lesson ? [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'html_content' => $htmlContent,
                ] : null,
            ],
        ]);

        // When returning gated content, opt out of any caching layer.
        if ($htmlContent !== null) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        }

        return $response;
    }

    /**
     * List playbooks the authenticated user has paid for.
     */
    public function myPlaybooks(Request $request)
    {
        $user = $this->getAuthenticatedUser($request);
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'data' => [],
            ], 401);
        }

        $playbooks = Course::where('content_type', 'playbook')
            ->whereHas('enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('payment_status', 'completed');
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Course $course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail_url' => $course->thumbnail_url,
                    'content_type' => $course->content_type,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $playbooks,
        ]);
    }

    /**
     * Get authenticated user from middleware (for protected routes).
     */
    private function getAuthenticatedUser(Request $request)
    {
        return Auth::user() ?? $request->auth_user;
    }

    /**
     * Optionally resolve a user from a Bearer token on a public route.
     * Mirrors ApiTokenAuth::handle() but returns null silently when invalid.
     */
    private function resolveBearerUser(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader) {
            return null;
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token, true);
        if ($decoded === false) {
            return null;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 2) {
            return null;
        }

        [$userId, $tokenHash] = $parts;
        $user = User::find($userId);
        if (! $user || $user->api_token !== $tokenHash) {
            return null;
        }
        if (isset($user->token_expires_at) && $user->token_expires_at && now()->isAfter($user->token_expires_at)) {
            return null;
        }

        return $user;
    }
}
