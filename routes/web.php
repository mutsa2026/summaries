<?php

use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('summaries.index');
});

Route::resource('summaries', SummaryController::class);
Route::post('/summaries/{summary}/regenerate', [SummaryController::class, 'regenerateSummary'])
    ->name('summaries.regenerate');