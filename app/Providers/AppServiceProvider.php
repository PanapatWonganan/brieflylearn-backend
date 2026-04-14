<?php

namespace App\Providers;

use App\Events\LessonCompleted;
use App\Events\CourseCompleted;
use App\Listeners\AwardGardenRewardsForLesson;
use App\Listeners\AwardGardenRewardsForCourse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Register Garden-Course integration event listeners
        Event::listen(
            LessonCompleted::class,
            AwardGardenRewardsForLesson::class,
        );

        Event::listen(
            CourseCompleted::class,
            AwardGardenRewardsForCourse::class,
        );

        // Configure Rate Limiting
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Login rate limiter - 5 attempts per minute
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Register rate limiter - 3 attempts per minute
        RateLimiter::for('register', function ($request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // API rate limiter - 60 requests per minute per user or IP
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Upload rate limiter - 10 uploads per hour per user or IP
        RateLimiter::for('upload', function ($request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
