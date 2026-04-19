<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollCourseRequest;
use App\Mail\CourseEnrollmentMail;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\MetaConversionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function getMyEnrollments(Request $request)
    {
        try {
            // Get authenticated user from middleware
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'enrollments' => []
                ], 401);
            }

            $userId = $user->id;

            // Get all courses (simulate enrollment - everyone enrolled in all courses)
            $courses = Course::with(['instructor:id,full_name', 'lessons'])
                ->select('id', 'title', 'description', 'instructor_id', 'price', 'level')
                ->take(10)
                ->get()
                ->map(function ($course) use ($userId) {
                    // Calculate progress
                    $totalLessons = Lesson::where('course_id', $course->id)->count();
                    $completedLessons = LessonProgress::where('user_id', $userId)
                        ->whereIn('lesson_id', function($query) use ($course) {
                            $query->select('id')
                                  ->from('lessons')
                                  ->where('course_id', $course->id);
                        })
                        ->where('is_completed', true)
                        ->count();

                    $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'instructor' => $course->instructor ? $course->instructor->full_name : 'Unknown',
                        'level' => $course->level,
                        'progress' => $progress,
                        'total_lessons' => $totalLessons,
                        'completed_lessons' => $completedLessons,
                        'enrolled_at' => now()->format('Y-m-d H:i:s'),
                        'status' => $progress >= 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'not_started')
                    ];
                });

            return response()->json([
                'success' => true,
                'enrollments' => $courses,
                'total_enrolled' => $courses->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch enrollments', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลการลงทะเบียนได้ กรุณาลองใหม่อีกครั้ง',
                'enrollments' => []
            ], 500);
        }
    }

    public function enrollInCourse(EnrollCourseRequest $request)
    {
        try {
            $validated = $request->validated();

            // Get authenticated user
            $user = Auth::user() ?? $request->auth_user;

            // In a real app, you'd create enrollment records
            // For now, just return success (auto-enrollment system)

            $course = Course::with('instructor:id,full_name')->find($validated['course_id']);

            // Get total lessons count for the course
            $totalLessons = Lesson::where('course_id', $course->id)->count();

            // Prepare course data for email
            $courseData = (object)[
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'level' => $course->level,
                'instructor' => $course->instructor ? $course->instructor->full_name : null,
                'total_lessons' => $totalLessons,
            ];

            // Send enrollment confirmation email (wrapped in try-catch)
            if ($user) {
                try {
                    Mail::to($user->email)->queue(new CourseEnrollmentMail($user, $courseData));
                } catch (\Exception $e) {
                    Log::warning('Failed to send course enrollment email', [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail enrollment if email fails
                }
            }

            // Track enrollment with Meta Conversions API
            try {
                $metaConversions = app(MetaConversionsService::class);
                $metaConversions->trackEnrollment($user, $request, $course, $request->input('meta_event_id'));
            } catch (\Exception $e) {
                Log::warning('Meta CAPI: AddToCart failed', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Enrolled successfully in ' . $course->title,
                'enrollment' => [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'enrolled_at' => now()->format('Y-m-d H:i:s'),
                    'status' => 'active'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Enrollment failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'การลงทะเบียนล้มเหลว กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }
}