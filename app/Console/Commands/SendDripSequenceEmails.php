<?php

namespace App\Console\Commands;

use App\Mail\DripSequenceMail;
use App\Models\EmailSequenceSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendDripSequenceEmails extends Command
{
    protected $signature = 'emails:drip-sequence';

    protected $description = 'Send drip email sequence (storytelling marketing emails)';

    public function handle(): int
    {
        $subscriptions = EmailSequenceSubscription::with(['user', 'sequence.steps'])
            ->dueToSend()
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('No drip emails to send.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            $step = $subscription->getNextStep();

            if (!$step) {
                // No more active steps — mark completed
                $subscription->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'next_send_at' => null,
                ]);
                $this->info("Completed sequence for {$subscription->user->email}");
                continue;
            }

            try {
                $unsubscribeUrl = URL::to('/api/v1/email/unsubscribe?' . http_build_query([
                    'token' => base64_encode($subscription->user->id . '|' . $subscription->sequence_id),
                ]));

                Mail::to($subscription->user->email)->send(
                    new DripSequenceMail($subscription->user, $step, $unsubscribeUrl)
                );

                $subscription->advance();
                $sent++;

                $this->info("Sent step {$step->step_number} to {$subscription->user->email} [{$subscription->sequence->name}]");
            } catch (\Exception $e) {
                $failed++;
                Log::warning('Failed to send drip sequence email', [
                    'user_id' => $subscription->user->id,
                    'sequence_id' => $subscription->sequence_id,
                    'step_number' => $step->step_number,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed for {$subscription->user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Drip sequence complete. Sent: {$sent}, Failed: {$failed}");
        return Command::SUCCESS;
    }
}
