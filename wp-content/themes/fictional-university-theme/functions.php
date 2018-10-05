<?php

// Custom REST API

function universityRegisterSearch() {
   register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'universitySearchResults'
    )); 
}

add_action('rest_api_init', 'universityRegisterSearch');

function universitySearchResults($data) {
    $mainQuery = new WP_Query(array(
        'post_type' => array(
            'post', 'page', 'professor', 'program', 'campus', 'event'
        ),
        's' => sanitize_text_field($data['term']) 
    ));
    
        $results = array(
            'generalInfo' => array(),
            'professors' => array(),
            'programs' => array(),
            'campuses' => array(),
            'events' => array(),
        );
    
        while($mainQuery->have_posts()) {
            $mainQuery->the_post();
            
            if (get_post_type() == 'post' OR get_post_type() == 'page') {
               array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
            if (get_post_type() == 'professor') {
               array_push($results['professors'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
            if (get_post_type() == 'program') {
               array_push($results['programs'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
            if (get_post_type() == 'campus') {
               array_push($results['campuses'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
            if (get_post_type() == 'event') {
               array_push($results['events'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
        }
        
        return $results;
}

// ----------------------------- //


function university_cutom_rest () {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));
}

add_action('rest_api_init', 'university_cutom_rest');


function pageBanner ($args = NULL) {
    
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    
    if (!$args['photo']) {
        if (get_field('page_banner_background_image')) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
            <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle'] ?></p>
            </div>
        </div>
    </div>
<?php }



function university_files() {
    
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=' , NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('search', get_theme_file_uri('/js/search.js'), NULL, '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i" rel="stylesheet');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
    wp_localize_script('search', 'universityData', array(
        'root_url' => get_site_url()
    ));
}

add_action('wp_enqueue_scripts', 'university_files');


function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');


function university_adjust_queries($query) {
    if (!is_admin() AND is_post_type_archive('campus') AND $query -> is_main_query()) {
        $query -> set('posts_per_page', '-1');
    }
    
    if (!is_admin() AND is_post_type_archive('program') AND $query -> is_main_query()) {
        $query -> set('order','title');
        $query -> set('order', 'ASC');
        $query -> set('posts_per_page', '-1');
    }
    
    if (!is_admin() AND is_post_type_archive('event') AND $query -> is_main_query()) {
        $today = date('Ymd');
        
        $query -> set('meta_key', 'event_date');
        $query -> set('orderby', 'meta_value_num');
        $query -> set('order', 'ASC');
        $query -> set('meta_query', array(
                            array(
                            'key' => 'event_date',
                            'compare' => '>=',
                            'value' => $today,
                            'type' => 'numeric'
                            )
                        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');


?>