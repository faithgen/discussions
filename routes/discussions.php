<?php

use Faithgen\Discussions\Http\Controllers\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::prefix('discussions')
    ->name('discussions')
    ->group(function () {
        Route::post('', [DiscussionController::class, 'create'])->name('create');
    });
