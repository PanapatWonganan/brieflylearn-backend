<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateChallengeProgressRequest;
use App\Models\DailyChallenge;
use App\Models\UserChallengeProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChallengeController extends Controller
{
    /**
     * Get today's challenges
     */
    public function getTodayChallenges(Request $request): JsonResponse
    {
        try {
            // For testing - use demo user if no auth
            $user = Auth::user() ?? $request->auth_user;

            // Auto-create today's challenges if none exist
            if (DailyChallenge::today()->active()->count() === 0) {
                $this->autoCreateDailyChallenges();
            }

            $challenges = DailyChallenge::getTodayChallengesForUser($user->id);

            // สถิติของวันนี้
            $todayStats = [
                'total_challenges' => count($challenges),
                'completed_challenges' => count(array_filter($challenges, fn($c) => $c['is_completed'])),
                'completion_percentage' => count($challenges) > 0 
                    ? round((count(array_filter($challenges, fn($c) => $c['is_completed'])) / count($challenges)) * 100, 2)
                    : 0,
                'total_xp_available' => array_sum(array_column($challenges, 'xp_reward')),
                'total_star_seeds_available' => array_sum(array_column($challenges, 'star_seeds_reward')),
                'xp_earned_today' => array_sum(array_map(fn($c) => $c['is_completed'] ? $c['xp_reward'] : 0, $challenges)),
                'star_seeds_earned_today' => array_sum(array_map(fn($c) => $c['is_completed'] ? $c['star_seeds_reward'] : 0, $challenges))
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'challenges' => $challenges,
                    'stats' => $todayStats,
                    'date' => today()->format('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get today\'s challenges', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลภารกิจประจำวันได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    /**
     * Auto-create daily challenges when none exist for today.
     * Rotates through different challenge sets for variety.
     */
    private function autoCreateDailyChallenges(): void
    {
        $dayOfWeek = now()->dayOfWeek; // 0=Sun, 1=Mon, ...

        $challengeSets = [
            // Set 0 (Sun): Focus on learning
            [
                ['name' => 'เรียนรู้สิ่งใหม่', 'description' => 'เรียนบทเรียนให้จบ 1 บทเรียน', 'challenge_type' => 'course_complete', 'requirements' => ['type' => 'complete_lesson', 'count' => 1], 'xp_reward' => 100, 'star_seeds_reward' => 50],
                ['name' => 'ดูแลโปรเจกต์', 'description' => 'รดน้ำพืชในสวน 1 ต้น', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'water_plants', 'count' => 1], 'xp_reward' => 50, 'star_seeds_reward' => 20],
                ['name' => 'เริ่มต้นใหม่', 'description' => 'ปลูกพืชใหม่ 1 ต้น', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'plant_seed', 'count' => 1], 'xp_reward' => 75, 'star_seeds_reward' => 30],
            ],
            // Set 1 (Mon): Focus on plant care
            [
                ['name' => 'ดูแลสวนตอนเช้า', 'description' => 'รดน้ำพืชในสวน 2 ต้น', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'water_plants', 'count' => 2], 'xp_reward' => 60, 'star_seeds_reward' => 25],
                ['name' => 'ขยายสวน', 'description' => 'ปลูกพืชใหม่ 1 ต้น', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'plant_seed', 'count' => 1], 'xp_reward' => 75, 'star_seeds_reward' => 30],
                ['name' => 'พัฒนาทักษะ', 'description' => 'เรียนบทเรียน 1 บท', 'challenge_type' => 'course_complete', 'requirements' => ['type' => 'complete_lesson', 'count' => 1], 'xp_reward' => 100, 'star_seeds_reward' => 50],
            ],
            // Set 2 (Tue-Sat): Mixed
            [
                ['name' => 'รดน้ำสวนประจำวัน', 'description' => 'รดน้ำพืชอย่างน้อย 1 ต้น', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'water_plants', 'count' => 1], 'xp_reward' => 50, 'star_seeds_reward' => 20],
                ['name' => 'เรียนรู้เพิ่มเติม', 'description' => 'เรียนจบ 1 บทเรียน', 'challenge_type' => 'course_complete', 'requirements' => ['type' => 'complete_lesson', 'count' => 1], 'xp_reward' => 100, 'star_seeds_reward' => 50],
                ['name' => 'สร้างโปรเจกต์ AI', 'description' => 'ปลูกพืชใหม่ในห้องปฏิบัติการ', 'challenge_type' => 'plant_care', 'requirements' => ['type' => 'plant_seed', 'count' => 1], 'xp_reward' => 75, 'star_seeds_reward' => 30],
            ],
        ];

        $setIndex = $dayOfWeek <= 1 ? $dayOfWeek : 2;
        $challenges = $challengeSets[$setIndex];

        foreach ($challenges as $challengeData) {
            $challengeData['available_date'] = today();
            $challengeData['is_active'] = true;
            DailyChallenge::create($challengeData);
        }
    }

    /**
     * Create sample daily challenges for testing
     */
    public function createSampleChallenges(): JsonResponse
    {
        try {
            $challenges = [
                [
                    'name' => '🚰 รดน้ำต้นไม้ในสวน',
                    'description' => 'รดน้ำให้กับต้นไม้ในสวนของคุณ 1 ครั้ง',
                    'challenge_type' => 'water_plants',
                    'requirements' => json_encode(['count' => 1, 'type' => 'water']),
                    'xp_reward' => 50,
                    'star_seeds_reward' => 10,
                    'available_date' => today(),
                    'expires_at' => today()->endOfDay(),
                    'is_active' => true
                ],
                [
                    'name' => '🌱 ปลูกพืชใหม่',
                    'description' => 'ปลูกต้นไม้ใหม่ในสวนของคุณ 1 ต้น',
                    'challenge_type' => 'plant_seed',
                    'requirements' => json_encode(['count' => 1, 'type' => 'plant']),
                    'xp_reward' => 100,
                    'star_seeds_reward' => 20,
                    'available_date' => today(),
                    'expires_at' => today()->endOfDay(),
                    'is_active' => true
                ],
                [
                    'name' => '📚 เรียนบทเรียนใหม่',
                    'description' => 'ดูบทเรียนใหม่ในคอร์สเรียน 1 บทเรียน',
                    'challenge_type' => 'complete_lesson',
                    'requirements' => json_encode(['count' => 1, 'type' => 'lesson']),
                    'xp_reward' => 75,
                    'star_seeds_reward' => 15,
                    'available_date' => today(),
                    'expires_at' => today()->endOfDay(),
                    'is_active' => true
                ]
            ];

            $created = [];
            foreach ($challenges as $challengeData) {
                // Check if challenge already exists today
                $existing = DailyChallenge::where('challenge_type', $challengeData['challenge_type'])
                    ->where('available_date', today())
                    ->first();
                    
                if (!$existing) {
                    $challenge = DailyChallenge::create($challengeData);
                    $created[] = $challenge;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sample challenges created',
                'created_count' => count($created),
                'total_challenges' => DailyChallenge::today()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create challenges', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถสร้างภารกิจตัวอย่างได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    /**
     * Get challenge history
     */
    public function getChallengeHistory(Request $request): JsonResponse
    {
        try {
            // For testing - use demo user if no auth
            $user = Auth::user() ?? $request->auth_user;
            $days = $request->query('days', 7); // Default 7 days

            $startDate = now()->subDays($days);
            $endDate = now();

            $challenges = DailyChallenge::dateRange($startDate, $endDate)
                ->active()
                ->with(['userProgress' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                }])
                ->orderBy('available_date', 'desc')
                ->get()
                ->map(function($challenge) {
                    $progress = $challenge->userProgress->first();
                    return [
                        'id' => $challenge->id,
                        'name' => $challenge->name,
                        'description' => $challenge->description,
                        'challenge_type' => $challenge->challenge_type,
                        'xp_reward' => $challenge->xp_reward,
                        'star_seeds_reward' => $challenge->star_seeds_reward,
                        'available_date' => $challenge->available_date->format('Y-m-d'),
                        'is_completed' => $progress ? $progress->is_completed : false,
                        'progress' => $progress ? $progress->progress : 0,
                        'target' => $progress ? $progress->target : ($challenge->requirements['count'] ?? 1),
                        'completed_at' => $progress?->completed_at?->format('Y-m-d H:i:s')
                    ];
                });

            // สถิติประวัติ
            $historyStats = [
                'total_challenges' => $challenges->count(),
                'completed_challenges' => $challenges->where('is_completed', true)->count(),
                'completion_rate' => $challenges->count() > 0 
                    ? round(($challenges->where('is_completed', true)->count() / $challenges->count()) * 100, 2)
                    : 0,
                'total_xp_earned' => $challenges->where('is_completed', true)->sum('xp_reward'),
                'total_star_seeds_earned' => $challenges->where('is_completed', true)->sum('star_seeds_reward'),
                'streak_days' => $this->calculateStreak($user->id),
                'best_category' => $this->getBestCategory($user->id, $days)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'challenges' => $challenges->groupBy('available_date'),
                    'stats' => $historyStats,
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => $endDate->format('Y-m-d'),
                        'days' => $days
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get challenge history', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลประวัติภารกิจได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    /**
     * Update challenge progress
     */
    public function updateProgress(UpdateChallengeProgressRequest $request, string $challengeId): JsonResponse
    {
        try {
            $validated = $request->validated();

            // For testing - use demo user if no auth
            $user = Auth::user() ?? $request->auth_user;
            $challenge = DailyChallenge::active()->find($challengeId);

            if (!$challenge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge not found or not available'
                ], 404);
            }

            // ตรวจสอบว่าเป็น challenge ของวันนี้หรือไม่
            if (!$challenge->available_date->isToday()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge is not available today'
                ], 400);
            }

            DB::beginTransaction();

            // เริ่มหรืออัปเดต progress
            $userProgress = $challenge->getUserProgress($user->id);
            if (!$userProgress) {
                $userProgress = $challenge->startForUser($user->id);
            }

            if ($userProgress->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge already completed'
                ], 400);
            }

            $increment = $validated['increment'] ?? 1;
            $progressData = $validated['progress_data'] ?? [];

            $wasCompleted = $userProgress->updateProgress($increment, $progressData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $wasCompleted ? 'Challenge completed!' : 'Progress updated',
                'data' => [
                    'challenge' => [
                        'id' => $challenge->id,
                        'name' => $challenge->name,
                        'is_completed' => $userProgress->is_completed
                    ],
                    'progress' => [
                        'current' => $userProgress->progress,
                        'target' => $userProgress->target,
                        'percentage' => $userProgress->progress_percentage,
                        'remaining' => $userProgress->remaining,
                        'status' => $userProgress->status
                    ],
                    'rewards' => $wasCompleted ? [
                        'xp' => $challenge->xp_reward,
                        'star_seeds' => $challenge->star_seeds_reward,
                        'message' => "🎉 คุณได้รับ {$challenge->xp_reward} XP และ {$challenge->star_seeds_reward} Star Seeds!"
                    ] : null
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update challenge progress', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตความคืบหน้าของภารกิจได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    /**
     * Get challenge leaderboard
     */
    public function getLeaderboard(Request $request): JsonResponse
    {
        try {
            $period = $request->query('period', 'week'); // week, month, all-time
            $limit = $request->query('limit', 10);

            $startDate = match($period) {
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'all-time' => now()->subYear(),
                default => now()->startOfWeek()
            };

            $leaderboard = UserChallengeProgress::query()
                ->select('user_id')
                ->selectRaw('COUNT(*) as challenges_completed')
                ->selectRaw('SUM(CASE WHEN challenges.xp_reward IS NOT NULL THEN challenges.xp_reward ELSE 0 END) as total_xp')
                ->selectRaw('SUM(CASE WHEN challenges.star_seeds_reward IS NOT NULL THEN challenges.star_seeds_reward ELSE 0 END) as total_star_seeds')
                ->join('daily_challenges as challenges', 'user_challenge_progress.challenge_id', '=', 'challenges.id')
                ->where('user_challenge_progress.is_completed', true)
                ->where('user_challenge_progress.completed_at', '>=', $startDate)
                ->with('user:id,full_name,avatar_url')
                ->groupBy('user_id')
                ->orderByDesc('challenges_completed')
                ->orderByDesc('total_xp')
                ->limit($limit)
                ->get()
                ->map(function($item, $index) {
                    return [
                        'rank' => $index + 1,
                        'user' => [
                            'id' => $item->user->id,
                            'name' => $item->user->full_name,
                            'avatar_url' => $item->user->avatar_url
                        ],
                        'challenges_completed' => $item->challenges_completed,
                        'total_xp' => $item->total_xp,
                        'total_star_seeds' => $item->total_star_seeds
                    ];
                });

            // หาตำแหน่งของผู้ใช้ปัจจุบัน
            // For testing - use demo user if no auth
            $user = Auth::user() ?? $request->auth_user;
            $userRank = UserChallengeProgress::query()
                ->select('user_id')
                ->selectRaw('COUNT(*) as challenges_completed')
                ->join('daily_challenges as challenges', 'user_challenge_progress.challenge_id', '=', 'challenges.id')
                ->where('user_challenge_progress.is_completed', true)
                ->where('user_challenge_progress.completed_at', '>=', $startDate)
                ->groupBy('user_id')
                ->orderByDesc('challenges_completed')
                ->pluck('user_id')
                ->search($user->id);

            $userStats = UserChallengeProgress::query()
                ->selectRaw('COUNT(*) as challenges_completed')
                ->selectRaw('SUM(CASE WHEN challenges.xp_reward IS NOT NULL THEN challenges.xp_reward ELSE 0 END) as total_xp')
                ->selectRaw('SUM(CASE WHEN challenges.star_seeds_reward IS NOT NULL THEN challenges.star_seeds_reward ELSE 0 END) as total_star_seeds')
                ->join('daily_challenges as challenges', 'user_challenge_progress.challenge_id', '=', 'challenges.id')
                ->where('user_challenge_progress.user_id', $user->id)
                ->where('user_challenge_progress.is_completed', true)
                ->where('user_challenge_progress.completed_at', '>=', $startDate)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'leaderboard' => $leaderboard,
                    'user_stats' => [
                        'rank' => $userRank !== false ? $userRank + 1 : null,
                        'challenges_completed' => $userStats->challenges_completed ?? 0,
                        'total_xp' => $userStats->total_xp ?? 0,
                        'total_star_seeds' => $userStats->total_star_seeds ?? 0
                    ],
                    'period' => $period,
                    'start_date' => $startDate->format('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get leaderboard', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลกระดานคะแนนได้ กรุณาลองใหม่อีกครั้ง'
            ], 500);
        }
    }

    /**
     * Calculate user's challenge streak
     */
    private function calculateStreak(string $userId): int
    {
        $streak = 0;
        $currentDate = today();

        while (true) {
            $challengesOnDate = DailyChallenge::where('available_date', $currentDate)
                ->whereHas('userProgress', function($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->where('is_completed', true);
                })
                ->count();

            $totalChallengesOnDate = DailyChallenge::where('available_date', $currentDate)->count();

            // ถ้าทำ challenge ครบในวันนั้น
            if ($challengesOnDate > 0 && $challengesOnDate == $totalChallengesOnDate) {
                $streak++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get user's best challenge category
     */
    private function getBestCategory(string $userId, int $days): ?string
    {
        $categoryStats = UserChallengeProgress::query()
            ->selectRaw('challenges.challenge_type, COUNT(*) as completed_count')
            ->join('daily_challenges as challenges', 'user_challenge_progress.challenge_id', '=', 'challenges.id')
            ->where('user_challenge_progress.user_id', $userId)
            ->where('user_challenge_progress.is_completed', true)
            ->where('user_challenge_progress.completed_at', '>=', now()->subDays($days))
            ->groupBy('challenges.challenge_type')
            ->orderByDesc('completed_count')
            ->first();

        return $categoryStats?->challenge_type;
    }
}