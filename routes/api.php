<?php

use App\Http\Controllers\Api\v1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourContoller;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('travels', [TravelController::class, 'index']);

Route::get('travels/{travel:slug}/tours', [TourContoller::class, 'index']);

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('login', [LoginController::class, 'login'])->name('login');


Route::prefix('admin')->middleware(['auth:sanctum, role:admin'])->group(function () {
    Route::post('travels', [AdminTravelController::class, 'store']);
});
