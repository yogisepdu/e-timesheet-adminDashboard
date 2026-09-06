<?php

use App\Http\Controllers\Admin\TimeSheetPdfController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/admin/time-sheets/{timeSheet}/pdf',
        [TimeSheetPdfController::class, 'download'],
    )->name('time-sheets.pdf.download');
});
