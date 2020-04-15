<?php

use Faithgen\Discussions\Http\Controllers\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::prefix('discussions')
    ->name('discussions')
    ->group(function () {
        Route::get('', [DiscussionController::class, 'index'])->name('index');
        Route::delete('{discussion}', [DiscussionController::class, 'destroy'])->name('destroy');
        Route::post('', [DiscussionController::class, 'create'])->name('create');
        Route::post('update', [DiscussionController::class, 'update'])->name('update');
    });
