<?php

use App\Http\Controllers\Api\v1\Admin\TourController as AdminTourController;
use App\Http\Controllers\Api\v1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('cors')->group(function () {
    
    Route::get('travels', [TravelController::class, 'index']);

    Route::get('travels/{travel:slug}/tours', [TourController::class, 'index']);

    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::get('login', [LoginController::class, 'login'])->name('login');

    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
        Route::middleware('role:admin')->group(function () {

            Route::post('travels', [AdminTravelController::class, 'store']);
            Route::post('travels/{travel:slug}/tour', [AdminTourController::class, 'store']);

        });
    Route::get('/user', function (Request $request) {
    return $request->user();
    });

    Route::put('travels/{travel}', [AdminTravelController::class, 'update'])->middleware('role:editor');
});

});

