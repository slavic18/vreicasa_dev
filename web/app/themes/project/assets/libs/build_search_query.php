<?php

class BuildSearchQuery
{
    public $query = [];
    public $args = [];
    public $filterOptions = [];
    public $postType = 'appartments';
    public $priceField = 'price';
    public $areaMinField = 'suprafata_min';
    public $areaMaxField = 'suprafata_max';
    const PROJECTS_POST_TYPE = 'projects';
    const APARTMENTS_POST_TYPE = 'projects';

    public function __construct($args)
    {
        $this->filterOptions = require_once __DIR__ . '/filter_options.php';
        $this->args = $args;
    }

    public function updatePostType()
    {
        $queriedObject = get_queried_object();
        if ($queriedObject) {
            $this->query['post_type'] = $queriedObject->name;
            $this->postType = $queriedObject->name;
            $this->priceField = $this->postType == 'projects' ? 'pret_minim' : 'price';

        }
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
        if (!empty($this->args['min_price']) && !empty($this->args['max_price'])) {
            $this->query['meta_query'][] = [
                'key' => $this->priceField,
                'value' => [sanitize_text_field($this->args['min_price']), sanitize_text_field($this->args['max_price'])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'

            ];
        } elseif (!empty($this->args['min_price'])) {
            $this->query['meta_query'][] = [
                'key' => $this->priceField,
                'value' => sanitize_text_field($this->args['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC'

            ];
        } elseif (!empty($this->args['max_price'])) {
            $this->query['meta_query'][] = [
                'key' => $this->priceField,
                'value' => sanitize_text_field($this->args['max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC'

            ];
        }
        if (!empty($this->args['range'])) {
            $range = explode(',', $this->args['range']);
            $this->query['meta_query'][] = [
                'key' => $this->priceField,
                'value' => [sanitize_text_field($range[0]), sanitize_text_field($range[1])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
        }

    }

    public function updateAreaQuery()
    {
        if ($this->postType == self::PROJECTS_POST_TYPE) {
            if (!empty($this->args['min_area'])) {
                $this->query['meta_query'][] = [
                    'key' => $this->areaMinField,
                    'value' => sanitize_text_field($this->args['min_area']),
                    'compare' => '<=',
                    'type' => 'NUMERIC'

                ];
            }
            if (!empty($this->args['max_area'])) {
                $this->query['meta_query'][] = [
                    'key' => $this->areaMaxField,
                    'value' => sanitize_text_field($this->args['max_area']),
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ];
            }
            return false;
        }


        if (!empty($this->args['min_area']) && !empty($this->args['max_area'])) {
            $this->query['meta_query'][] = [
                'key' => 'area',
                'value' => [sanitize_text_field($this->args['min_area']), sanitize_text_field($this->args['max_area'])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
        } elseif (!empty($this->args['min_area'])) {
            $this->query['meta_query'][] = [
                'key' => 'area',
                'value' => sanitize_text_field($this->args['min_area']),
                'compare' => '>=',
                'type' => 'NUMERIC'

            ];
        } elseif (!empty($this->args['max_area'])) {
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
        $minFloor = 0;
        $maxFloor = 0;
        if (isset($this->args['floor_range'])) {
            foreach ($this->args['floor_range'] as $key => $floor) {
                switch ($key) {
                    case '1-4':
                        $minFloor = 1;
                        $maxFloor = max(4, $maxFloor);
                        break;
                    case '5-10':
                        $minFloor = $minFloor ? min(5, $minFloor) : 5;
                        $maxFloor = max(10, $maxFloor);
                        break;
                    case '10+':
                        $minFloor = $minFloor ? min(10, $minFloor) : 10;
                        $maxFloor = max(99, $maxFloor);
                        break;
                }

            }
            $this->query['meta_query'][] = [
                'key' => 'floor',
                'value' => [$minFloor, $maxFloor],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
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
                    $this->query['meta_key'] = $this->priceField;
                    $this->query['meta_type'] = 'NUMERIC';
                    $this->query['orderby'] = 'meta_value_num';
                    $this->query['order'] = 'ASC';
                case 'price-desc':
                    $this->query['meta_key'] = $this->priceField;
                    $this->query['meta_type'] = 'NUMERIC';
                    $this->query['orderby'] = 'meta_value_num';
                    $this->query['order'] = 'DESC';
                    break;
            }
        }
    }

    public function updatePostsPerPageQuery()
    {
        $this->query['posts_per_page'] = 12;
        if (isset($this->args['perPage'])) {
            $this->query['posts_per_page'] = sanitize_text_field($this->args['perPage']);
        }

    }

    public function buildQuery()
    {
        // build custom logic query

        if ($this->args['paged']) {
            $this->query['paged'] = $this->args['paged'];
        }
        $this->query['post_type'] = 'apartments';
        $this->query['meta_query']['relation'] = 'AND';

        // post type
        $this->updatePostType();

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
//        deploy($this->query, false);
//        deploy([$this->query, get_posts($this->query)]);
        query_posts($this->query);
//        deploy($this->query);
    }
}