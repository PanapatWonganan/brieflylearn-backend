<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LessonProgress;
use App\Models\GardenActivity;
use App\Mail\WeeklyProgressMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWeeklyProgressEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:weekly-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly progress summary every Monday morning';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all active users (active in last 30 days) who haven't received weekly email this week
        $users = User::whereNotNull('last_active_at')
            ->where('last_active_at', '>=', now()->subDays(30))
            ->where(function ($q) {
                $q->whereNull('weekly_email_sent_at')
                  ->orWhere('weekly_email_sent_at', '<', now()->startOfWeek());
            })
            ->get();
            
        foreach ($users as $user) {
            // Gather stats for the past week
            $weekStart = now()->startOfWeek()->subWeek();
            $weekEnd = now()->startOfWeek();
            
            $stats = [
                'lessons_completed' => LessonProgress::where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->whereBetween('completed_at', [$weekStart, $weekEnd])
                    ->count(),
                'xp_earned' => GardenActivity::where('user_id', $user->id)
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('xp_earned'),
                'streak_days' => $user->current_streak ?? 0,
                'garden_level' => $user->garden?->level ?? 1,
            ];
            
            // Only send if there's some activity
            if ($stats['lessons_completed'] > 0 || $stats['xp_earned'] > 0) {
                try {
                    Mail::to($user->email)->send(new WeeklyProgressMail($user, $stats));
                    $user->update(['weekly_email_sent_at' => now()]);
                    $this->info("Sent weekly progress to {$user->email}");
                } catch (\Exception $e) {
                    Log::warning("Failed weekly progress email", ['user' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        }
        
        $this->info("Weekly progress emails processed.");
        return Command::SUCCESS;
    }
}
