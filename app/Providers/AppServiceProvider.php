<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Add this custom validation rule.
        Validator::extend('alpha_spaces', function ($attribute, $value) {
        // This will only accept alpha and spaces.
        // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s0-9 .-]+$/u', $value);
        });
        //Add this custom validation rule.
        Validator::extend('my_alpha', function ($attribute, $value) {
        // This will only accept alpha and spaces.
        // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s .,\-""()]+$/u', $value);
        });

        //Add this custom validation rule.
        Validator::extend('phone_number', function ($attribute, $value) {
        
            return preg_match('/^[z0-9- ().+\/]+$/u', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
