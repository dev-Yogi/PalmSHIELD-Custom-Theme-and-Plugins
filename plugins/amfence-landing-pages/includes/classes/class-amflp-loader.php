<?php

class AMFLP_Loader {
    
    private $id;
    private $data;
    private $data_type;
    
    public function __construct( $type, $id ) {
        $this->id = $id;
        $this->data_type = $type;
        $this->setData($type);
    }
    
    private function getData($a_type){
        if ($a_type === 'city') {
            $meta_keys = array_keys(get_registered_meta_keys('city'));
            $meta_keys = array_diff($meta_keys, ['_content_ids']);
            $data = [];
            foreach ($meta_keys as $key) {
                $meta_value = get_post_meta(get_the_ID(), $key, true);
                $data[$key] = $meta_value;
            }
            return $data;
        } elseif ($a_type === 'content') {
            $meta_data = get_post_meta($this->id, '_content_ids', true);
            if (!$meta_data) {
                $sections = get_terms([
                    'taxonomy' => 'content_sections',
                    'hide_empty' => false,
                    'fields' => 'slugs'
                ]);
                $post_ids = [];
                foreach ($sections as $section) {
                    $ids = $this->getContent($section);
                    if ($ids) {
                        $index = array_rand($ids);
                        $post_ids[$section] = $ids[$index];
                    }
                }
                // Specify the desired order of sections
                $ordered_keys = [
                    'page-intro-section',
                    'products-section',
                    'why-us-section',
                    'dealer-network-section',
                    'project-gallery-section',
                    'faq-section',
                    'free-quote-cta-section'
                ];
                $ordered = [];
                foreach ($ordered_keys as $key) {
                    if (isset($post_ids[$key])) {
                        $ordered[$key] = $post_ids[$key];
                    }
                }
                update_post_meta($this->id, '_content_ids', $ordered);
                return $ordered;
            }
            return $meta_data;
        }
    }
    
    private function getContent($term){
        $args = [
            'post_type'     => 'content',
            'fields'        => 'ids',
            'numberposts'   => -1,
            'tax_query'     => [
                [
                    'taxonomy'  => 'content_sections',
                    'terms'     => $term,
                    'field'     => 'slug'
                ],
            ],
        ];
        $temp = get_posts($args);
        return $temp;
    }
    
    private function setData($type){ 
        $this->data = $this->getData($type);
    }
    
    public function theData(){ 
        return $this->data;
    }
}