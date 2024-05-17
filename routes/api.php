<?php

use App\Http\Controllers\Api\v1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\TourContoller;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('travels', [TravelController::class, 'index']);

Route::get('travels/{travel:slug}/tours', [TourContoller::class, 'index']);

Route::prefix('admin')->group(function () {
    Route::post('travels', [AdminTravelController::class, 'store']);
});