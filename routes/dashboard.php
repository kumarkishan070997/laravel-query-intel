<?php

use Illuminate\Support\Facades\Route;
use Kishan\QueryIntel\Http\Controllers\DashboardController;

Route::middleware(config('query-intel.dashboard.middleware'))
    ->prefix(config('query-intel.dashboard.path'))
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])
            ->name('query-intel.dashboard');

        Route::get('/requests/{requestId}', [DashboardController::class, 'show'])
            ->name('query-intel.request.show');
    });
