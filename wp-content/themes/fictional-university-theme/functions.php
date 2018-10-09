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
                'url' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            )); 
            }
            if (get_post_type() == 'professor') {
               array_push($results['professors'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            )); 
            }
            if (get_post_type() == 'program') {
                $relatedCampuses = get_field('related_campus');
                
                if($relatedCampuses) {
                  foreach($relatedCampuses as $campus) {
                        array_push($results['campuses'], array(
                            'title' => get_the_title($campus),
                            'url' => get_the_permalink($campus)
                        ));
                    }  
                }
                
               array_push($results['programs'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'id' => get_the_id()
            )); 
            }
            if (get_post_type() == 'campus') {
               array_push($results['campuses'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            )); 
            }
            if (get_post_type() == 'event') {
                
               $the_event_date = get_field( 'event_date', false, false );
                
               $description = null;
               if (has_excerpt()) {
                    $description = get_the_excerpt();
               } else {
                    $description = wp_trim_words(get_the_content(), 18);
               }
                
               $the_event_date = new DateTime( $the_event_date );
                
               array_push($results['events'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'month' => $the_event_date->format('M'),
                'day' => $the_event_date->format('d'),
                'description' => $description
            )); 
            }
        }
    
        if($results['programs']) {
            
            $programsMetaQuery = array('relation' => 'OR');
    
            foreach($results['programs'] as $item) {
                array_push($programsMetaQuery, array(
                        'key' => related_programs,
                        'compare' => 'LIKE',
                        'value' => '"' . $item['id'] . '"'
                    ));
            }
    
            $programRelationshipQuery = new WP_Query(array(
                'post_type' => array('professor', 'event'),
                'meta_query' => $programsMetaQuery
            ));
    
            while($programRelationshipQuery->have_posts()) {
                $programRelationshipQuery->the_post();

                if (get_post_type() == 'event') {
                
               $the_event_date = get_field( 'event_date', false, false );
                
               $description = null;
               if (has_excerpt()) {
                    $description = get_the_excerpt();
               } else {
                    $description = wp_trim_words(get_the_content(), 18);
               }
                
               $the_event_date = new DateTime( $the_event_date );
                
               array_push($results['events'], array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'month' => $the_event_date->format('M'),
                'day' => $the_event_date->format('d'),
                'description' => $description
            )); 
            }
                
                if (get_post_type() == 'professor') {
                   array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'url' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                )); 
                }
            }
            
            $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
            $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        }
    
        return $results;
}

// ----------------------------- //


function university_custom_rest () {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {
            return count_user_posts(get_current_user_id(), note);
        }
    ));
}

add_action('rest_api_init', 'university_custom_rest');


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
            <h1 class="page-banner__title">
                <?php echo $args['title'] ?>
            </h1>
            <div class="page-banner__intro">
                <p>
                    <?php echo $args['subtitle'] ?>
                </p>
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
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
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

// Redirect subscriber account out of dmin and onto homepage

function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();
    
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('admin_init', 'redirectSubsToFrontend');


// No admin bar for suscribers

function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();
    
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

add_action('wp_loaded', 'noSubsAdminBar');


// Customize Login Screen

function ourHeaderUrl () {
    return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'ourHeaderUrl');


// Customize Login Logo

function ourLoginCSS() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i" rel="stylesheet');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
}

add_action('login_enqueue_scripts', ourLoginCSS);

// Custom Login Title

function ourLoginTitle() {
    return get_bloginfo('name');
}

add_filter('login_headertitle', 'ourLoginTitle');
    
// Force note posts to be private

function makeNotePrivate($data, $postarr) {
   
  if($data['post_type'] == 'note') {
    $user_id = get_current_user_id();
    
    if(count_user_posts(get_current_user_id(), 'note') > 2 AND !$postarr['ID'] AND $user_id != 1) {
        die("You have reached your note limit");
    }
      
    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }
    
  if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
    $data['post_status'] = "private";
  }
  
  return $data;
}

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 5);

?>