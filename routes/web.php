<?php

use Illuminate\Support\Facades\Route;
use PivoAndCode\WordpressMeilisearch\Http\Controllers\PostsController;

//add_action('init', function(){
//    $post_id = 1587528;
//    build_item_document( get_post( $post_id, ARRAY_A ), get_post( $post_id ));
//});


Route::prefix( 'meilisearch' )->group( function () {
    Route::get( 'posts', [ PostsController::class, 'index' ] );
} );
