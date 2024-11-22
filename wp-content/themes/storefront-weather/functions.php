<?php
function my_child_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'my_child_theme_enqueue_styles');


function register_cities_post_type() {
    $labels = array(
        'name'               => 'Cities',
        'singular_name'      => 'City',
        'menu_name'          => 'Cities',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New City',
        'edit_item'          => 'Edit City',
        'new_item'           => 'New City',
        'view_item'          => 'View City',
        'all_items'          => 'All Cities',
        'search_items'       => 'Search Cities',
        'not_found'          => 'No Cities found',
        'not_found_in_trash' => 'No Cities found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'cities'),
        'supports'           => array('title', 'editor'),
        'menu_icon'          => 'dashicons-location-alt',
        'show_in_rest'       => true,
    );

    register_post_type('cities', $args);
}
add_action('init', 'register_cities_post_type');


function add_cities_meta_box() {
    add_meta_box(
        'city_meta_box',
        'City Details',
        'display_cities_meta_box',
        'cities',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_cities_meta_box');

function display_cities_meta_box($post) {
    $latitude = get_post_meta($post->ID, 'city_latitude', true);
    $longitude = get_post_meta($post->ID, 'city_longitude', true);

    echo '<label for="city_latitude">Latitude:</label>';
    echo '<input type="text" name="city_latitude" id="city_latitude" value="' . esc_attr($latitude) . '" class="widefat">';
    echo '<label for="city_longitude">Longitude:</label>';
    echo '<input type="text" name="city_longitude" id="city_longitude" value="' . esc_attr($longitude) . '" class="widefat">';
}

function save_city_meta_box($post_id) {
    if (array_key_exists('city_latitude', $_POST)) {
        update_post_meta($post_id, 'city_latitude', sanitize_text_field($_POST['city_latitude']));
    }
    if (array_key_exists('city_longitude', $_POST)) {
        update_post_meta($post_id, 'city_longitude', sanitize_text_field($_POST['city_longitude']));
    }
}
add_action('save_post', 'save_city_meta_box');


function register_countries_taxonomy() {
    $labels = array(
        'name'              => 'Countries',
        'singular_name'     => 'Country',
        'search_items'      => 'Search Countries',
        'all_items'         => 'All Countries',
        'edit_item'         => 'Edit Country',
        'update_item'       => 'Update Country',
        'add_new_item'      => 'Add New Country',
        'new_item_name'     => 'New Country Name',
        'menu_name'         => 'Countries',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'countries'),
    );

    register_taxonomy('countries', 'cities', $args);
}
add_action('init', 'register_countries_taxonomy');


?>