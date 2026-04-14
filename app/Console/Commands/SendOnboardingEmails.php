<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\OnboardingDay1Mail;
use App\Mail\FirstCourseNudgeMail;
use App\Mail\GardenIntroMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOnboardingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:onboarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send onboarding email series to new users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Step 2: Day 1 onboarding (24+ hours after registration, onboarding_step = 1)
        $day1Users = User::where('onboarding_step', 1)
            ->where('created_at', '<=', now()->subHours(24))
            ->where('created_at', '>=', now()->subDays(3)) // Don't send to very old users
            ->get();
            
        foreach ($day1Users as $user) {
            try {
                Mail::to($user->email)->send(new OnboardingDay1Mail($user));
                $user->update(['onboarding_step' => 2]);
                $this->info("Sent Day 1 onboarding to {$user->email}");
            } catch (\Exception $e) {
                Log::warning("Failed onboarding day 1 email", ['user' => $user->id, 'error' => $e->getMessage()]);
            }
        }
        
        // Step 3: First course nudge (72+ hours, onboarding_step = 2, no lesson progress)
        $nudgeUsers = User::where('onboarding_step', 2)
            ->where('created_at', '<=', now()->subHours(72))
            ->whereDoesntHave('lessonProgress', function ($q) {
                $q->where('is_completed', true);
            })
            ->get();
            
        foreach ($nudgeUsers as $user) {
            try {
                Mail::to($user->email)->send(new FirstCourseNudgeMail($user));
                $user->update(['onboarding_step' => 3]);
                $this->info("Sent course nudge to {$user->email}");
            } catch (\Exception $e) {
                Log::warning("Failed course nudge email", ['user' => $user->id, 'error' => $e->getMessage()]);
            }
        }
        
        // Also advance users who DID complete lessons past step 2/3
        User::where('onboarding_step', 2)
            ->where('created_at', '<=', now()->subHours(72))
            ->whereHas('lessonProgress', function ($q) {
                $q->where('is_completed', true);
            })
            ->update(['onboarding_step' => 3]);
        
        // Step 4: Garden intro (7+ days, onboarding_step = 3)
        $gardenUsers = User::where('onboarding_step', 3)
            ->where('created_at', '<=', now()->subDays(7))
            ->get();
            
        foreach ($gardenUsers as $user) {
            try {
                Mail::to($user->email)->send(new GardenIntroMail($user));
                $user->update(['onboarding_step' => 4]);
                $this->info("Sent garden intro to {$user->email}");
            } catch (\Exception $e) {
                Log::warning("Failed garden intro email", ['user' => $user->id, 'error' => $e->getMessage()]);
            }
        }
        
        $this->info("Onboarding emails processed.");
        return Command::SUCCESS;
    }
}
