<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MasterDataController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TimeSheetController;
use App\Http\Controllers\Api\TimeSheetPdfController;

/*
|--------------------------------------------------------------------------
| Public API
|--------------------------------------------------------------------------
|
| Endpoint yang dapat digunakan tanpa Sanctum token.
|
*/

Route::post('/login', [
    AuthController::class,
    'login',
]);

/*
|--------------------------------------------------------------------------
| Protected API
|--------------------------------------------------------------------------
|
| Seluruh endpoint di bawah ini membutuhkan:
|
| Authorization: Bearer {token}
|
*/

Route::middleware('auth:sanctum')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [
        AuthController::class,
        'me',
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */

    Route::get('/master-data', [
        MasterDataController::class,
        'index',
    ]);

    Route::get('/contractors', [
        MasterDataController::class,
        'contractors',
    ]);

    Route::get('/operators', [
        MasterDataController::class,
        'operators',
    ]);

    Route::get('/equipment-units', [
        MasterDataController::class,
        'equipmentUnits',
    ]);

    Route::get('/activities', [
        MasterDataController::class,
        'activities',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Time Sheet
    |--------------------------------------------------------------------------
    */
    Route::get('/time-sheets', [
        TimeSheetController::class,
        'index',
    ]);

    Route::get('/time-sheets/{timeSheet}', [
        TimeSheetController::class,
        'show',
    ]);

    Route::post('/time-sheets/draft', [
        TimeSheetController::class,
        'storeDraft',
    ]);

    Route::put('/time-sheets/{timeSheet}/draft', [
        TimeSheetController::class,
        'updateDraft',
    ]);

    Route::post('/time-sheets/submit', [
        TimeSheetController::class,
        'storeAndSubmit',
    ]);

    Route::post('/time-sheets/{timeSheet}/submit', [
        TimeSheetController::class,
        'submitDraft',
    ]);

    Route::get('/time-sheets/{timeSheet}/pdf', [
        TimeSheetPdfController::class,
        'download',
    ]);
});
