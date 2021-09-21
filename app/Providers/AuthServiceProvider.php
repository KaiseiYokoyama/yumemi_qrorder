<?php

namespace App\Providers;

use App\Models\Party;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // cookieのsession_secretを使って、食べる人であることを認証する
        Auth::viaRequest('session-secret', function (Request $request) {
            $session_secret = $request->cookie('session_secret');

            return Party::query()
                ->where('uuid', $session_secret)
                ->first()
                ;
        });
    }
}
