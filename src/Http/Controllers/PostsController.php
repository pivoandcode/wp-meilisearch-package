<?php

namespace PivoAndCode\WordpressMeilisearch\Http\Controllers;

use Illuminate\Http\Request;
use PivoAndCode\WordpressMeilisearch\Actions\RenderMeiliProductsAction;

class PostsController {
    public function index( Request $request, RenderMeiliProductsAction $getProductsAction) {
        return $getProductsAction->handle($request->all());
    }
}
