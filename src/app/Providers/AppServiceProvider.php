<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Libraries\DateHelper;
use Illuminate\Support\Facades\Validator;

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
        /**/  // https://laravel.com/docs/8.x/container#binding-a-singleton
        
        $this->app->singleton('AuthMgr', function ($app) {
            return new \App\Http\Libraries\AuthMgr($app);
        });
             
        $this->app->singleton('PageMgr', function ($app) {
            return new \App\Http\Libraries\PageMgr($app);
        });
            
        $this->app->singleton('PageFactory', function ($app) {
            return new \App\Http\Libraries\PageFactory($app);
        });
        
        $this->app->singleton('MenuMgr', function ($app) {
            return new \App\Http\Libraries\MenuMgr($app);
        });
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        Validator::extend('date_thai', function($attribute, $value, $parameters, $validator) {
            if (empty($value)) return true;
            return DateHelper::isValidThaiDate($value);
        });  
        
    }
}
