<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $view->with([
                    'navUnreadCount' => $user->unreadNotifications()->count(),
                    'navNotifications' => $user->notifications()
                        ->latest()
                        ->limit(5)
                        ->get(),
                ]);
            } else {
                $view->with([
                    'navUnreadCount' => 0,
                    'navNotifications' => collect(),
                ]);
            }
        });
    }
}
