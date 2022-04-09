<?php

namespace App\Providers;

use App\Collections\TimesCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(TimesCollection::class,function(){
            $times = collect(Config::get('times'));
            return new TimesCollection($times);
        });


        //

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        //
        Collection::macro('week', fn() =>
            Collection::times(7, fn($day) => now()->subDay()->addDays($day))
        );



    }
}
