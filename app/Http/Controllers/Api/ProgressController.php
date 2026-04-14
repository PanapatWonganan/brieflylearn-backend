<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLessonProgressRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgressController extends Controller
{
    public function getMySummary(Request $request)
    {
        try {
            // Get authenticated user from middleware
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'summary' => []
                ], 401);
            }

            $userId = $user->id;

            // Get overall progress summary
            $totalCourses = Course::count();
            $totalLessons = Lesson::count();
            $completedLessons = LessonProgress::where('user_id', $userId)
                ->where('is_completed', true)
                ->count();

            // Calculate courses progress
            $coursesProgress = Course::select('id', 'title', 'description')
                ->take(10)
                ->get()
                ->map(function ($course) use ($userId) {
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
                        'course_id' => $course->id,
                        'course_title' => $course->title,
                        'total_lessons' => $totalLessons,
                        'completed_lessons' => $completedLessons,
                        'progress_percentage' => $progress,
                        'status' => $progress >= 100 ? 'completed' : ($progress > 0 ? 'in_progress' : 'not_started')
                    ];
                });

            // Calculate recent activity (last 7 days)
            $recentActivity = LessonProgress::where('user_id', $userId)
                ->where('is_completed', true)
                ->where('updated_at', '>=', now()->subDays(7))
                ->count();

            // Calculate study streak
            $studyStreak = 0;
            $currentDate = now()->startOfDay();
            for ($i = 0; $i < 30; $i++) {
                $hasActivityOnDate = LessonProgress::where('user_id', $userId)
                    ->where('is_completed', true)
                    ->whereDate('updated_at', $currentDate->copy()->subDays($i))
                    ->exists();
                
                if ($hasActivityOnDate) {
                    $studyStreak++;
                } else {
                    break;
                }
            }

            // Get learning statistics
            $weeklyStats = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $lessonsCompleted = LessonProgress::where('user_id', $userId)
                    ->where('is_completed', true)
                    ->whereDate('updated_at', $date)
                    ->count();
                
                $weeklyStats[] = [
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->format('l'),
                    'lessons_completed' => $lessonsCompleted
                ];
            }

            $overallProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            return response()->json([
                'success' => true,
                'summary' => [
                    'overall_progress' => [
                        'total_courses' => $totalCourses,
                        'total_lessons' => $totalLessons,
                        'completed_lessons' => $completedLessons,
                        'progress_percentage' => $overallProgress
                    ],
                    'study_streak' => [
                        'current_streak' => $studyStreak,
                        'unit' => 'days'
                    ],
                    'recent_activity' => [
                        'lessons_this_week' => $recentActivity,
                        'week_start' => now()->subDays(7)->format('Y-m-d'),
                        'week_end' => now()->format('Y-m-d')
                    ],
                    'weekly_stats' => $weeklyStats,
                    'courses_progress' => $coursesProgress->toArray(),
                    'achievements' => [
                        'total_completed_courses' => $coursesProgress->where('status', 'completed')->count(),
                        'courses_in_progress' => $coursesProgress->where('status', 'in_progress')->count(),
                        'courses_not_started' => $coursesProgress->where('status', 'not_started')->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch progress summary', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลความคืบหน้าได้ กรุณาลองใหม่อีกครั้ง',
                'summary' => []
            ], 500);
        }
    }

    public function updateLessonProgress(UpdateLessonProgressRequest $request, $lessonId)
    {
        try {
            $validated = $request->validated();

            // Get authenticated user from middleware
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $userId = $user->id;

            // Check if lesson exists
            $lesson = Lesson::find($lessonId);
            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lesson not found'
                ], 404);
            }

            // Find the user's enrollment for this lesson's course (REQUIRED)
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('course_id', $lesson->course_id)
                ->first();

            // Authorization: User must be enrolled in the course
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่ได้ลงทะเบียนในคอร์สนี้'
                ], 403);
            }

            // Build update data — only include is_completed if explicitly sent
            $updateData = [
                'watch_time' => $validated['watch_time'] ?? 0,
                'enrollment_id' => $enrollment->id
            ];

            // Only update is_completed if it was explicitly provided in the request
            if (array_key_exists('is_completed', $validated)) {
                $updateData['is_completed'] = $validated['is_completed'];
                $updateData['completed_at'] = $validated['is_completed'] ? now() : null;
            }

            // Update or create lesson progress
            $progress = LessonProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'lesson_id' => $lessonId
                ],
                $updateData
            );

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully',
                'progress' => [
                    'lesson_id' => $progress->lesson_id,
                    'is_completed' => $progress->is_completed,
                    'watch_time' => $progress->watch_time,
                    'completed_at' => $progress->completed_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $progress->updated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update progress', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตความคืบหน้าได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    public function getCourseProgress(Request $request, $courseId)
    {
        try {
            // Get authenticated user from middleware
            $user = Auth::user() ?? $request->auth_user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $userId = $user->id;

            // Check if course exists
            $course = Course::with('instructor:id,full_name')->find($courseId);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Get course lessons
            $lessons = Lesson::where('course_id', $courseId)
                ->select('id', 'title', 'description', 'duration')
                ->get();

            // Fetch all lesson progress for this user+course in ONE query (prevent N+1)
            $lessonIds = $lessons->pluck('id');
            $progressMap = LessonProgress::where('user_id', $userId)
                ->whereIn('lesson_id', $lessonIds)
                ->get()
                ->keyBy('lesson_id');

            // Map lessons with progress data
            $lessons = $lessons->map(function ($lesson) use ($progressMap) {
                $progress = $progressMap->get($lesson->id);

                // Calculate progress percentage with defensive null check and cap at 100%
                $progressPercentage = 0;
                if ($progress && !empty($lesson->duration) && $lesson->duration > 0) {
                    $progressPercentage = min(100, round(($progress->watch_time / $lesson->duration) * 100));
                }

                return [
                    'lesson_id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'duration' => $lesson->duration,
                    'is_completed' => $progress ? $progress->is_completed : false,
                    'watch_time' => $progress ? $progress->watch_time : 0,
                    'completed_at' => $progress && $progress->completed_at
                        ? $progress->completed_at->format('Y-m-d H:i:s')
                        : null,
                    'progress_percentage' => $progressPercentage
                ];
            });

            $totalLessons = $lessons->count();
            $completedLessons = $lessons->where('is_completed', true)->count();
            $courseProgress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            return response()->json([
                'success' => true,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'instructor' => $course->instructor ? $course->instructor->full_name : 'Unknown',
                    'level' => $course->level
                ],
                'progress' => [
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'progress_percentage' => $courseProgress,
                    'status' => $courseProgress >= 100 ? 'completed' : ($courseProgress > 0 ? 'in_progress' : 'not_started')
                ],
                'lessons' => $lessons->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch course progress', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลความคืบหน้าของคอร์สได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }
}