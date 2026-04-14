<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email Automation Scheduled Tasks
// Note: Timezone is set to UTC in config/app.php
// Thailand is UTC+7, so schedule times are adjusted accordingly

// Onboarding series - run every hour between 1:00-2:00 AM UTC (8:00-9:00 AM Thai time)
Schedule::command('emails:onboarding')->hourly()->between('1:00', '2:00');

// Weekly progress - every Monday at 1:00 AM UTC (8:00 AM Thai time)
Schedule::command('emails:weekly-progress')->weeklyOn(1, '1:00');

// Inactive reminders - daily at 11:00 AM UTC (6:00 PM Thai time)
Schedule::command('emails:inactive-reminders')->dailyAt('11:00');

// Streak milestones - daily at 1:30 AM UTC (8:30 AM Thai time), after streaks are calculated
Schedule::command('emails:streak-milestones')->dailyAt('1:30');

// Drip email marketing sequence - hourly at 2:00 AM UTC (9:00 AM Thai time)
// Sends storytelling emails to subscribed users based on their individual schedule
Schedule::command('emails:drip-sequence')->hourly()->between('2:00', '3:00');
