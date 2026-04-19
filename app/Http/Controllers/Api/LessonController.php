<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * Get authenticated user from middleware
     */
    private function getAuthenticatedUser(Request $request)
    {
        return Auth::user() ?? $request->auth_user;
    }

    /**
     * Optionally resolve a user from a Bearer token on a public route.
     *
     * Mirrors `ApiTokenAuth::handle()` but returns `null` silently when the
     * header is absent or invalid, so public endpoints continue to serve
     * guests while paying users get richer responses.
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
        $user = \App\Models\User::find($userId);
        if (! $user || $user->api_token !== $tokenHash) {
            return null;
        }
        if (isset($user->token_expires_at) && $user->token_expires_at && now()->isAfter($user->token_expires_at)) {
            return null;
        }

        return $user;
    }

    /**
     * Get all lessons for a course.
     *
     * This endpoint is in the public route group so guest previews work, but we
     * honor an optional Bearer token to surface purchase state for logged-in
     * users. That lets the frontend render paid lessons as clickable links and
     * flip the hero CTA from "buy" to "start learning" once the user paid.
     */
    public function getCourseLessons(Request $request, $courseId)
    {
        $course = Course::with(['lessons' => function($query) {
            $query->with('primaryVideo')
                  ->orderBy('order_index', 'asc');
        }])->findOrFail($courseId);

        // Optionally resolve the caller and compute purchase state for the course.
        $userHasPaidAccess = false;
        $user = $this->getAuthenticatedUser($request) ?: $this->resolveBearerUser($request);
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

        $lessons = $course->lessons->map(function ($lesson) use ($userHasPaidAccess) {
            $video = $lesson->primaryVideo;

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'duration_minutes' => $lesson->duration_minutes,
                'order_index' => $lesson->order_index,
                'is_free' => $lesson->is_free,
                'locked' => ! ($lesson->is_free || $userHasPaidAccess),
                'video_url' => $lesson->video_url, // External URL if any
                'video' => $video ? [
                    'id' => $video->id,
                    'status' => $video->status,
                    'duration' => $video->formatted_duration,
                    'size' => $video->formatted_size,
                    'ready' => $video->isReady(),
                ] : null,
                'created_at' => $lesson->created_at,
            ];
        });

        return response()->json([
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'price' => $course->price,
                'original_price' => $course->original_price,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
            ],
            'lessons' => $lessons,
            'total_lessons' => $lessons->count(),
            'user_has_paid_access' => $userHasPaidAccess,
        ]);
    }
    
    /**
     * Get single lesson with video details
     */
    public function show(Request $request, $lessonId)
    {
        $lesson = Lesson::with(['primaryVideo', 'course'])->findOrFail($lessonId);
        $video = $lesson->primaryVideo;

        // Determine whether the requesting user is allowed to watch:
        //  - Free lessons are always watchable.
        //  - Otherwise require a completed enrollment for the parent course.
        //  - Courses with price = 0 unlock once an enrollment row exists.
        //
        // NOTE: This endpoint is mounted in the public route group (no auth.api
        // middleware) so that course/lesson previews work for guests. We still
        // honor a Bearer token when the caller provides one, so logged-in
        // purchasers get `can_watch: true` for their paid lessons.
        $canWatch = (bool) $lesson->is_free;
        if (! $canWatch) {
            $user = $this->getAuthenticatedUser($request) ?: $this->resolveBearerUser($request);
            if ($user) {
                $enrollment = $user->enrollments()
                    ->where('course_id', $lesson->course_id)
                    ->orderByDesc('enrolled_at')
                    ->first();

                if ($enrollment) {
                    $coursePrice = (float) ($lesson->course?->price ?? 0);
                    $canWatch = $coursePrice <= 0
                        ? true
                        : $enrollment->payment_status === 'completed';
                }
            }
        }

        $lessonData = [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'duration_minutes' => $lesson->duration_minutes,
            'order_index' => $lesson->order_index,
            'is_free' => $lesson->is_free,
            'video_url' => $lesson->video_url,
            'course' => [
                'id' => $lesson->course->id,
                'title' => $lesson->course->title,
            ],
            'video' => null,
            'can_watch' => $canWatch,
        ];
        
        if ($video) {
            $lessonData['video'] = [
                'id' => $video->id,
                'status' => $video->status,
                'duration' => $video->formatted_duration,
                'size' => $video->formatted_size,
                'ready' => $video->isReady(),
                'processing_error' => $video->processing_error,
            ];
            
            // If video is ready and user can watch, provide streaming info
            if ($video->isReady() && $lessonData['can_watch']) {
                $lessonData['video']['stream_available'] = true;
            }
        }
        
        return response()->json($lessonData);
    }
    
    /**
     * Get streaming URL for a lesson's video
     */
    public function getStreamUrl(Request $request, $lessonId)
    {        
        $lesson = Lesson::with('readyVideo')->findOrFail($lessonId);
        $video = $lesson->readyVideo;
        
        if (!$video || !$video->isReady()) {
            return response()->json([
                'message' => 'Video not available',
                'status' => $video ? $video->status : 'no_video'
            ], 400);
        }
        
        // Try to get authenticated user
        $user = $this->getAuthenticatedUser($request);

        // Check if user can watch (allow free lessons or paid enrolled users)
        if (!$lesson->is_free) {
            // Check if user is authenticated
            if (!$user) {
                return response()->json([
                    'message' => 'Authentication required to access this lesson'
                ], 401);
            }

            // Look up latest enrollment for this course
            $enrollment = $user->enrollments()
                ->where('course_id', $lesson->course_id)
                ->orderByDesc('enrolled_at')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'message' => 'This lesson requires course enrollment. Please enroll in the course first.',
                    'code' => 'enrollment_required',
                ], 403);
            }

            // Require a completed payment for paid courses.
            $coursePrice = (float) ($lesson->course?->price ?? 0);
            if ($coursePrice > 0 && $enrollment->payment_status !== 'completed') {
                return response()->json([
                    'message' => 'กรุณาชำระเงินก่อนเข้าเรียนคอร์สนี้',
                    'code' => 'payment_required',
                    'order_no' => $enrollment->order_no,
                    'payment_status' => $enrollment->payment_status,
                ], 402);
            }
        }

        // Generate signed URL - use anonymous for free lessons
        $expires = now()->addHours(2);
        $userId = $user ? $user->id : 'anonymous';
        
        $token = hash_hmac(
            'sha256',
            "{$video->id}:{$userId}:{$expires->timestamp}",
            config('app.key')
        );
        
        $streamUrl = route('api.video.stream', [
            'video' => $video->id,
            'user' => $userId,
            'expires' => $expires->timestamp,
            'token' => $token
        ]);
        
        return response()->json([
            'stream_url' => $streamUrl,
            'expires_at' => $expires->toISOString(),
            'video' => [
                'id' => $video->id,
                'title' => $lesson->title,
                'duration' => $video->formatted_duration,
            ],
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
            ]
        ]);
    }
    
    /**
     * Get all courses with lesson counts
     */
    public function getCourses()
    {
        $courses = Course::withCount('lessons')
            ->where('is_published', true)
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'instructor' => $course->instructor_name ?? 'Unknown',
                    'price' => $course->price,
                    'level' => $course->level,
                    'lessons_count' => $course->lessons_count,
                    'duration_minutes' => $course->duration_minutes,
                    'free_preview' => null,
                    'created_at' => $course->created_at,
                ];
            });
            
        return response()->json([
            'courses' => $courses,
            'total' => $courses->count()
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}