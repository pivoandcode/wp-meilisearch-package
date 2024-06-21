<?php

namespace PivoAndCode\WordpressMeilisearch\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SearchMeiliAction {
    public function handle(array $data){
        $client = app()->make(\Meilisearch\Client::class);

        $data = Validator::make( $data, [
            'post_type'      => [
                'required',
                Rule::in( app()->make( 'WordpressMeilisearch' )->getPostTypes() )
            ],
            'posts_per_page' => [ 'sometimes', 'filled', 'integer', 'min:1' ],
            'page'           => [ 'sometimes', 'filled', 'integer', 'min:0', 'max:5000' ],
            'sort_by'        => [ 'ends_with:asc,desc' ]
        ] )->validated();

        $filters = $this->buildFilters($data);

        $results = $client->index( $data['post_type'] )
          ->search(
              data_get( $data, 'q', '' ),
              array(
                  'sort'                 => array( data_get( $data, 'sort_by', 'updated_at:desc' ) ), // default value should be dynamic per index
                  'facets'               => array( '*' ),
                  'offset'               => (int) data_get( $data, 'posts_per_page', 18 ) * data_get( $data, 'page', '0' ),
                  'limit'                => (int) data_get( $data, 'posts_per_page', 18 ),
                  'filter'               => $filters,
                  'attributesToRetrieve' => array(
                      'name',
                      'brand',
                      'price',
                      'regular_price',
                      'sale_price',
                      'permalink',
                      'id',
                      'image',
                      'product_condition'
                  )
              )
          );

        return $results;
    }

    private function buildFilters( array $params ): array {
        $passedFilters = collect($params)
            ->filter(fn($value, $key) => !in_array($key, $this->getMeiliDefaultOptions()->all()));

        $shouldSkipKey = [];

        return $passedFilters->map(function($value, $key) use ($passedFilters, &$shouldSkipKey) {
            $strippedKey = str($key)->replaceMatches('/_lvl\d/', '')->value();

            if ( in_array( $key, $shouldSkipKey ) ){
                return false;
            }

            $groupedFilters = $passedFilters->where(
                fn($nestedValue, $nestedKey) => str($nestedKey)->contains($strippedKey)
            );

            if ($groupedFilters->count() > 1){
                return $groupedFilters->map(function($value, $key) use ($strippedKey, &$shouldSkipKey){
                    $shouldSkipKey[] = $key;

                    return collect( explode(',', $value[0]) )->map(
                        fn($explodedValue) => sprintf( "%s = '%s'", $strippedKey, $explodedValue )
                    )->values()->flatten()->all();
                })->values()->flatten()->all();
            } else if ( is_array( $value ) ){
                return collect( explode(',', $value[0]) )->map(
                    fn($explodedValue) => sprintf( "%s = '%s'", $strippedKey, urldecode($explodedValue) )
                )->values()->when(true, function($collection){
                    return $collection->count() == 1
                        ? $collection->first()
                        : $collection->all();
                } );
            } else if ( str($key)->startsWith('range-min-') ) {
                return sprintf('%s > %s', str($key)->remove('range-min-'), $value);
            } else if ( str($key)->startsWith('range-max-') ) {
                return sprintf('%s < %s', str($key)->remove('range-max-'), $value);
            }

            return [];
        })->filter()->values()->all();
    }

    private function getMeiliDefaultOptions(): Collection {
        return collect(array(
            'sort_by',
            'post_type',
            'page',
            'posts_per_page'
        ));
    }
}
