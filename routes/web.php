<?php

use Illuminate\Support\Facades\Route;
use PivoAndCode\WordpressMeilisearch\Http\Controllers\PostsController;

Route::prefix( 'meilisearch' )->group( function () {
    Route::get( 'posts', [ PostsController::class, 'index' ] );
} );
