<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('App\Repositories\AuthRepository\AuthInterface','App\Repositories\AuthRepository\AuthRepo'); # Auth
        $this->app->bind('App\Repositories\LoanRepository\LoanInterface','App\Repositories\LoanRepository\LoanRepo'); # Loan
        $this->app->bind('App\Repositories\RepaymentRepository\RepaymentInterface','App\Repositories\RepaymentRepository\RepaymentRepo'); # Repayment
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
