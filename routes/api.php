<?php

use App\Http\Controllers\Api\V1\TourContoller;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('travel', [TravelController::class, 'index']);

Route::get('travel/{travel:slug}/tours', [TourContoller::class, 'index']);

