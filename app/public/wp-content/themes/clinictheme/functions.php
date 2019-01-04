<?php

/**
 * page_banner() handles banner area of a page.
 * If no $args are passed, the function will use default values.
 * 
 * @args: associative array(dictionary)
 * @param: title, subtitle, banner_image
 * @returns: HTML and CSS snippet for banner area. 
 * dependencies: Advanced Custom Fields plugin for get_field() function
 */
function page_banner($args = NULL){
    if (!$args['title']){
        $args['title'] = get_the_title();
    }
    if (!$args['subtitle']){
        /* Grab subtitle from WP-admin area */
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!$args['image']){
        if (get_field('page_banner_background_image')){
            /* Grab banner image from WP-admin area */
            $args['image'] = get_field('page_banner_background_image')['sizes']['page-banner'];
        } else {
            /* Default banner-image if all other options fail */
            $args['image'] = get_theme_file_uri('/images/dogs-whitebg.jpg');
        }
    } /* HTML/CSS snippet returned to function caller */?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php 
            echo $args['image']; ?>);">
        </div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"> <?php echo $args['title']; ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div> <?php
}

/**
 * function to load CSS and JavaScript 
 */
function clinic_resources(){
    /* JavaScript */
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyDzoEZVm8qLGy6Pog5Ob-xfh3Cv5YgwgrM', NULL, '1.0', true);
    wp_enqueue_script('clinic_js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);
    wp_localize_script('clinic_js', 'clinic_data', array(
        'root_url' => get_site_url()
    ));
    /* CSS */
    wp_enqueue_style('font_google_roboto', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font_awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('clinic_styles', get_stylesheet_uri(), NULL, microtime());
}

/**
 * Theme Setup
 */
function clinic_features(){
    /* Add title to pages */
    add_theme_support('title-tag');
    /* enable featured images */
    add_theme_support('post-thumbnails');
    /* adding new image sizes */
    add_image_size('employee-landscape', 400, 260, true);
    add_image_size('employee-portrait', 480, 650, true);
    add_image_size('page-banner', 1500, 350, true);
    /* Add header menu to wp-admin */
    register_nav_menu('header_menu_location', 'Header Menu Location');
    register_nav_menu('footer_menu_location_left', 'Footer Menu Location Left');
    register_nav_menu('footer_menu_location_right', 'Footer Menu Location Right');
}

/**
 * Custom queries for fine grained filtering of custom post_types
 */
function clinic_custom_queries($query){
    /* Logic for sorting wp queries for post_type Locales */
    if (!is_admin() AND is_post_type_archive('locale') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

    /* Logic for sorting wp queries for post_type Treatment */
    if (!is_admin() AND is_post_type_archive('treatment') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }

    /* Logic for sorting wp queries for post_type Event */
    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => date('Ymd'),/* loads todays date for use in meta_query */
              'type' => 'numeric'
            )));
    }
}

/* Google Maps API-key */
function acf_google_maps_api_key($api){
    $api['key'] = 'AIzaSyDzoEZVm8qLGy6Pog5Ob-xfh3Cv5YgwgrM';
    return $api;
}

/**
 * REST API - register custom JSON fields
 */
function clinic_custom_rest() {
    register_rest_field('post', 'author_name', array(
        'get_callback' => function(){return get_the_author();}
    ));
}

/**
 * Hooks and scripts
 */
/* Add CSS and JS to be handled by Wordpress */
add_action('wp_enqueue_scripts', 'clinic_resources');
/* function to load CSS and JavaScript */
add_action('after_setup_theme', 'clinic_features');
/* hooking custom queries to Wordpress */
add_action('pre_get_posts', 'clinic_custom_queries');
/* Google maps API key */
add_filter('acf/fields/google_map/api', 'acf_google_maps_API_key');
/* REST API hook */
add_action('rest_api_init', 'clinic_custom_rest');

?>