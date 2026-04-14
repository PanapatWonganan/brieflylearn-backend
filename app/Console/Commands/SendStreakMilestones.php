<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\StreakMilestoneMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendStreakMilestones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:streak-milestones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send streak milestone celebration emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $milestones = [7, 30, 100];
        
        foreach ($milestones as $milestone) {
            // Find users who hit this streak today
            $users = User::where('current_streak', $milestone)
                ->where('last_streak_date', now()->toDateString())
                ->get();
                
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new StreakMilestoneMail($user, $milestone));
                    $this->info("Sent {$milestone}-day streak milestone to {$user->email}");
                } catch (\Exception $e) {
                    Log::warning("Failed streak milestone email", ['user' => $user->id, 'streak' => $milestone, 'error' => $e->getMessage()]);
                }
            }
        }
        
        $this->info("Streak milestones processed.");
        return Command::SUCCESS;
    }
}
