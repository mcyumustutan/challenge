<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::middleware('auth:api')->resource('device', App\Http\Controllers\DeviceController::class);
Route::post('/device/register', [App\Http\Controllers\DeviceController::class, 'register']);

Route::group([
    'prefix'=>'device',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('/purchase', [App\Http\Controllers\DeviceController::class, 'purchase']);
    Route::get('/checkSubscription', [App\Http\Controllers\DeviceController::class, 'checkSubscription']);
});
 