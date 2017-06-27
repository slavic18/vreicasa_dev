<?php

class BuildSearchQuery
{
    public $query = [];
    public $args = [];
    public $filterOptions = [];


    public function __construct($args)
    {
        $this->filterOptions = require_once __DIR__ . '/filter_options.php';
        $this->args = $args;
    }

    public function updateAddressQuery()
    {
        if ($this->args['address']) {
            $this->query['tax_query'] = [
                'relation' => 'AND', [
                    'taxonomy' => 'cities',
                    'relation' => 'AND',
                    'field' => 'name',
                    'terms' => sanitize_text_field($this->args['address'])
                ]
            ];
        }

    }

    public function updatePriceQuery()
    {
        if (isset($this->args['min_price']) && $this->args['max_price']) {
            $this->query['meta_query'][] = [
                'key' => 'price',
                'value' => [sanitize_text_field($this->args['min_price']), sanitize_text_field($this->args['max_price'])],
                'compare' => 'BETWEEN'
            ];
        } elseif (isset($this->args['min_price'])) {
            $this->query['meta_query'][] = [
                'key' => 'price',
                'value' => sanitize_text_field($this->args['min_price']),
                'compare' => '>='
            ];
        } elseif (isset($this->args['max_price'])) {
            $this->query['meta_query'][] = [
                'key' => 'price',
                'value' => sanitize_text_field($this->args['max_price']),
                'compare' => '<='
            ];
        }
        if (isset($this->args['range'])) {
            $range = explode(',', $this->args['range']);
            $this->query['meta_query'][] = [
                'key' => 'price',
                'value' => [sanitize_text_field($range[0]), sanitize_text_field($range[1])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
        }

    }

    public function updateAreaQuery()
    {
        if (isset($this->args['min_area']) && $this->args['max_area']) {
            $this->query['meta_query'][] = [
                'key' => 'area',
                'value' => [sanitize_text_field($this->args['min_area']), sanitize_text_field($this->args['max_area'])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
        } elseif (isset($this->args['min_area'])) {
            $this->query['meta_query'][] = [
                'key' => 'area',
                'value' => sanitize_text_field($this->args['min_area']),
                'compare' => '>=',
                'type' => 'NUMERIC'

            ];
        } elseif (isset($this->args['max_area'])) {
            $this->query['meta_query'][] = [
                'key' => 'area',
                'value' => sanitize_text_field($this->args['max_area']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            ];
        }
    }

    public function updateFloorQuery()
    {
        if (isset($this->args['floor_range'])) {
            switch ($this->args['floor_range']) {
                case '1-4':
                    $this->query['meta_query'][] = [
                        'key' => 'floor',
                        'value' => [1, 4],
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC'
                    ];
                    break;
                case '5-10':
                    $this->query['meta_query'][] = [
                        'key' => 'floor',
                        'value' => [5, 10],
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC'

                    ];
                    break;
                case '10':
                    $this->query['meta_query'][] = [
                        'key' => 'floor',
                        'value' => 10,
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    ];
                    break;
            }
        }

    }

    public function updateOtherOptionsQuery()
    {
        foreach ($this->args as $fieldK => $fieldV) {
            if (isset($this->filterOptions[$fieldK])) {
                switch ($this->filterOptions[$fieldK]['type']) {
                    case 'OR':
                        if (is_array($fieldV)) {

                            $values = [];
                            foreach ($fieldV as $key => $value) {
                                $values[] = sanitize_text_field($key);
                            }
                            $this->query['meta_query'][] = [
                                'key' => sanitize_text_field($fieldK),
                                'value' => $values,
                                'compare' => 'IN'
                            ];
                        }
                        break;
                    case 'bool':
                        $this->query['meta_query'][] = [
                            'key' => sanitize_text_field($fieldK),
                            'value' => 1,
                            'compare' => '='
                        ];
                        break;
                }
            }
        }
    }

    public function updateOrderByQuery()
    {
        if (isset($this->args['orderby'])) {
            switch ($this->args['orderby']) {
                case 'date-asc':
                    $this->query['orderby'] = 'date';
                    $this->query['order'] = 'ASC';
                    break;
                case 'date-desc':
                    $this->query['orderby'] = 'date';
                    $this->query['order'] = 'DESC';
                    break;
                case 'price-asc':
                    $this->query['meta_key'] = 'price';
                    $this->query['meta_type'] = 'NUMERIC';
                    $this->query['orderby'] = 'meta_value_num';
                    $this->query['order'] = 'ASC';
                case 'price-desc':
                    $this->query['order'] = 'DESC';
                    break;
            }
        }
    }

    public function updatePostsPerPageQuery()
    {
        if (isset($this->args['perPage'])) {
//            $this->query['posts_per_page'] = sanitize_text_field($this->args['perPage']);
        }
        $this->query['posts_per_page'] = 12;

    }

    public function buildQuery()
    {
        // build custom logic query

        if ($this->args['paged']) {
            $this->query['paged'] = $this->args['paged'];
        }
        $this->query['post_type'] = 'apartments';
        $this->query['meta_query']['relation'] = 'AND';

        // orderby
        $this->updateOrderByQuery();

        // perPage
        $this->updatePostsPerPageQuery();

        // price
        $this->updatePriceQuery();

        // area
        $this->updateAreaQuery();

        // floor
        $this->updateFloorQuery();

        // address
        $this->updateAddressQuery();

        // other options
        $this->updateOtherOptionsQuery();
//        deploy($this->query);
        query_posts($this->query);
//        deploy($this->query);
    }
}