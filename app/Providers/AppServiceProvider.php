<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\auth\SocialUserResolver;
// use Adaojunior\Passport\SocialUserResolverInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //dd(SocialUserResolverInterface::class);
        //
        // $this->app->singleton(SocialUserResolverInterface::class, SocialUserResolver::class);
        $this->app->bind('Adaojunior\Passport\SocialUserResolverInterface', SocialUserResolver::class);

    }
}
