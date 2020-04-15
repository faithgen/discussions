<?php

use Faithgen\Discussions\Http\Controllers\DiscussionController;
use Illuminate\Support\Facades\Route;

Route::prefix('discussions')
    ->name('discussions')
    ->group(function () {
        Route::get('', [DiscussionController::class, 'index'])->name('index');
        Route::get('{discussion}', [DiscussionController::class, 'show'])->name('show');
        Route::get('comments/{discussion}', [DiscussionController::class, 'comments'])->name('comments');
        Route::delete('{discussion}', [DiscussionController::class, 'destroy'])->name('destroy');
        Route::post('', [DiscussionController::class, 'create'])->name('create');
        Route::post('update', [DiscussionController::class, 'update'])->name('update');
        Route::post('comment', [DiscussionController::class, 'comment'])->name('comment');
    });
