<?php
/**
 * The template for displaying the city archive
 */
get_header();

$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'city'
));

$permus = calculatePageLayoutPermutations();


?><main id="site-content"><?php
?><section id="cities-section"><?php
if($posts) {
    ?><h3>List of Cities</h3><?php
    ?><ul class="city-list"><?php
    foreach ($posts as $p) {
        $perm = get_permalink($p);
        $name = $p->post_title;
        echo "<li><a href='" . $perm . "'>" . $name . '</a></li>';
    }
    ?></ul><?php
}
    ?></section><?php
    ?></main><?php
$sections = get_terms([
                'taxonomy' => 'content_sections',
                'hide_empty' => true,
                'fields' => 'slugs'
            ]);

function calculatePageLayoutPermutations() {
    $sections = get_terms([
        'taxonomy' => 'content_sections',
        'hide_empty' => true,
        'fields' => 'slugs'
    ]);
    //var_dump($sections);
    
    $totalSections = count($sections);
    $optionsArray = [];
    foreach ($sections as $section) {
        $ids = getContent($section);
        $count = count($ids);
        $optionsArray[$section] = $count;
    }
    
    //var_dump($optionsArray);
    $totalPerms = 1;
    foreach ($optionsArray as $section => $count) {
        $totalPerms *= $count;
    }
    
   
    
    $totalPerms *= factorial($totalSections);

    $returnVal = [
        'section-count' => $totalSections,
        'options-count' => $optionsArray,
        'total-permuts' => $totalPerms
    ];
    return $returnVal;
}

function getContent($term){
        
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

function factorial($n) {
    if ($n <= 1) {
        return 1;
    } else {
        return $n * factorial($n - 1);
    }
}

get_footer();