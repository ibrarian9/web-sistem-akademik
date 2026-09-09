<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Services\AuditLogger;

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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        View::addNamespace('layouts', resource_path('views/components/layouts'));
        View::addNamespace('layouts', resource_path('views/layouts'));

        // Register Audit Logging for Authentication Events
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            $role = $user->role->nama ?? 'User';
            AuditLogger::log('login', "Pengguna {$user->nama} ({$user->username}) berhasil login sebagai {$role}", $user, [
                'log_name' => 'autentikasi',
                'causer' => $user,
                'properties' => [
                    'role' => $role,
                    'guard' => $event->guard,
                ],
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            $credentials = $event->credentials ?? [];
            $username = $credentials['username'] ?? ($credentials['email'] ?? 'unknown');
            AuditLogger::log('failed_login', "Percobaan login gagal dengan username/kredensial '{$username}'", $event->user, [
                'log_name' => 'keamanan',
                'causer' => $event->user,
                'properties' => [
                    'attempted_username' => $username,
                    'guard' => $event->guard,
                ],
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                $user = $event->user;
                AuditLogger::log('logout', "Pengguna {$user->nama} ({$user->username}) logout dari sistem", $user, [
                    'log_name' => 'autentikasi',
                    'causer' => $user,
                    'properties' => [
                        'guard' => $event->guard,
                    ],
                ]);
            }
        });
    }
}
