<?php

namespace PivoAndCode\WordpressMeilisearch\Actions;

class RenderMeiliProductsAction {
    public function __construct(
        private SearchMeiliAction $searchMeiliAction,
        private GetFacetDataAction $getFacetDataAction
    ) {
    }

    public function handle(array $data): string{
        $meiliSearchData = $this->normalizeData($data);

        $results = $this->searchMeiliAction->handle($meiliSearchData);
        $facetData = $this->getFacetDataAction->handle($meiliSearchData['post_type']);

        ob_start();

        if ( $results->getHitsCount() ) {
            // TODO: dynamically find the {index}-holder. Throw exceptions if views are missing.
            echo \Roots\View('partials.meilisearch.archives.items', [
                'results' => $results->getHits(),
                'total_hits' => $results->getEstimatedTotalHits(),
                'total_pages' => ceil( $results->getEstimatedTotalHits() / (int) $meiliSearchData['posts_per_page'] ),
                'response' => $results,
                'page' =>  $meiliSearchData['current_page'],
                'sort_by' => $meiliSearchData['sort_by'],
                'facets' => $facetData,
                'params' => $data
            ] )->render();
        } else {
            echo \Roots\View('partials.meilisearch.archives.items', [
                'results' => [],
                'total_hits' => 0,
                'total_pages' => 1,
                'page' =>  $meiliSearchData['current_page'],
                'sort_by' => $meiliSearchData['sort_by'],
                'facets' => $facetData,
                'params' => $data
            ] )->render();
        }

        return ob_get_clean();
    }

    private function normalizeData( array $data ) {
        $data['current_page'] = data_get( $data, 'current_page', '1' );
        $data['posts_per_page'] = data_get( $data, 'posts_per_page', '18' );
        $data['sort_by'] = data_get( $data, 'sort_by', 'updated_at:desc' );
        $data['offset'] = (int) $data['posts_per_page'] * $data['current_page'];
        $data['limit'] = (int) $data['posts_per_page'];

        return $data;
    }
}
