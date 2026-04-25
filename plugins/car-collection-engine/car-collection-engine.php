<?php
/*
Plugin Name: Car Collection Engine
Description: Core data structures for the Car Dealership (CPT and Taxonomies).
Version: 1.0
Author: Shams
*/

// 1. Register the "Cars" Post Type
function create_car_post_type() {
    register_post_type('cars', array(
        'labels' => array(
            'name' => 'Cars',
            'singular_name' => 'Car',
            'add_new_item' => 'Add New Car',
            'edit_item' => 'Edit Car'
        ),
        'public'      => true,
        'has_archive' => true,
        'menu_icon'   => 'dashicons-performance', // Racing icon
        'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true, // Enables Gutenberg editor
    ));
}
add_action('init', 'create_car_post_type');

// 2. Register "Brands" Taxonomy (Like Categories but for Cars)
function create_car_taxonomies() {
    register_taxonomy('brand', 'cars', array(
        'labels' => array('name' => 'Brands', 'singular_name' => 'Brand'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'create_car_taxonomies');


// 3. Add Price Meta Box
function car_add_meta_boxes() {
    add_meta_box('car_details', 'Car Specifications', 'car_meta_box_html', 'cars', 'side');
}
add_action('add_meta_boxes', 'car_add_meta_boxes');

function car_meta_box_html($post) {
    $price = get_post_meta($post->ID, '_car_price', true);
    ?>
    <label for="car_price">Price ($):</label>
    <input type="number" name="car_price" id="car_price" value="<?php echo esc_attr($price); ?>" style="width:100%;">
    <?php
}

function car_save_meta($post_id) {
    if (array_key_exists('car_price', $_POST)) {
        update_post_meta($post_id, '_car_price', $_POST['car_price']);
    }
}
add_action('save_post', 'car_save_meta');