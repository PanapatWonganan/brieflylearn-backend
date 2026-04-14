<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\InactiveReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendInactiveReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:inactive-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to inactive users (3, 7, 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tiers = [
            ['days' => 3, 'max_days' => 6],   // 3-6 days inactive
            ['days' => 7, 'max_days' => 29],   // 7-29 days inactive
            ['days' => 30, 'max_days' => 60],  // 30-60 days inactive
        ];
        
        foreach ($tiers as $tier) {
            $users = User::whereNotNull('last_active_at')
                ->where('last_active_at', '<=', now()->subDays($tier['days']))
                ->where('last_active_at', '>=', now()->subDays($tier['max_days']))
                ->where(function ($q) use ($tier) {
                    $q->whereNull('inactive_email_sent_at')
                      ->orWhere('inactive_email_sent_at', '<', now()->subDays($tier['days']));
                })
                ->get();
                
            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new InactiveReminderMail($user, $tier['days']));
                    $user->update(['inactive_email_sent_at' => now()]);
                    $this->info("Sent {$tier['days']}-day inactive reminder to {$user->email}");
                } catch (\Exception $e) {
                    Log::warning("Failed inactive reminder", ['user' => $user->id, 'days' => $tier['days'], 'error' => $e->getMessage()]);
                }
            }
        }
        
        $this->info("Inactive reminders processed.");
        return Command::SUCCESS;
    }
}
