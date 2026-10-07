<?php

namespace App\Providers;

use App\Modules\Automation\Listeners\RunMessageAutomations;
use App\Modules\Messaging\Events\IncomingMessageReceived;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Rate limiter for Groq chat endpoint: per-user limits, fallback to IP
        Event::listen(IncomingMessageReceived::class, RunMessageAutomations::class);

        RateLimiter::for('groq', function (Request $request) {
            $user = $request->user();
            if ($user) {
                return Limit::perMinute(60)->by($user->id);
            }

            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
