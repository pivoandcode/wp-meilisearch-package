<?php

namespace PivoAndCode\WordpressMeilisearch;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Roots\Acorn\Application;

class WordpressMeilisearch
{
    /**
     * The application instance.
     *
     * @var \Roots\Acorn\Application
     */
    protected $app;

    /**
     * Create a new WordpressMeilisearch instance.
     *
     * @param  \Roots\Acorn\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getPostTypes(): Collection {
        $postTypes = Cache::get('post_types');

        if ( is_null($postTypes) ){
            $postTypes  = collect(get_post_types())->keys();
            Cache::set('post_types', $postTypes);
        }

        return $postTypes;
    }
}
