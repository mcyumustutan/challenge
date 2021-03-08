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

use App\Http\Controllers\DeviceController;

Route::post('/device/register', [DeviceController::class, 'register']);

Route::group([
    'prefix' => 'device',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('/purchase', [DeviceController::class, 'purchase']);
    Route::get('/checkSubscription', [DeviceController::class, 'checkSubscription']);
});
