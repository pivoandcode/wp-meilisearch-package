<?php

namespace PivoAndCode\WordpressMeilisearch\Actions;

use Illuminate\Support\Facades\Cache;
use Meilisearch\Client;

class GetFacetDataAction {
    public function __construct(
        private Client $client
    ) {}

    public function handle(string $postType): array{
        if (! $facetData =  Cache::get( $postType . '_facets') ){
            $facetData = $this->client
                ->index( $postType )
                ->search('',[ 'facets' => ['*'] ])
                ->getFacetDistribution();

            Cache::set($postType . '_facets', $facetData, 60 * 60 * 12);
        }

        return $facetData;
    }
}
