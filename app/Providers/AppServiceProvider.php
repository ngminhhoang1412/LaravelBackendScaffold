<?php

namespace App\Providers;

use App\Common\GlobalVariable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GlobalVariable::class, function (){
            return new GlobalVariable();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro(
            'withWhereHas',
            function ($relation, $constraint) {
                return $this
                    ->whereHas($relation, $constraint)
                    ->with($relation, $constraint);
            }
        );
    }
}
