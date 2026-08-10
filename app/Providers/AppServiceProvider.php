<?php

namespace App\Providers;

use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
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
        Relation::enforceMorphMap(['user' => User::class, 'creative_request' => CreativeRequest::class, 'deliverable' => Deliverable::class, 'deliverable_version' => DeliverableVersion::class]);
        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });
    }
}
