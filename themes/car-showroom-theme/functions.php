<?php
/**
 * Sets up theme supports and custom image sizes for the showroom theme.
 */
function shams_showroom_setup()
{
    add_theme_support('post-thumbnails');
    add_image_size('car-thumbnail', 600, 400, true);
}
add_action('after_setup_theme', 'shams_showroom_setup');

/**
 * Enqueues the main stylesheet for the showroom theme.
 */
function shams_enqueue_theme_assets()
{
    wp_enqueue_style(
        'shams-car-showroom-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'shams_enqueue_theme_assets');

/**
 * Returns the formatted car price for the current or provided post.
 *
 * @param int|null $post_id Optional post ID.
 * @return string
 */
function shams_get_formatted_car_price($post_id = null)
{
    $car_id = $post_id ? absint($post_id) : get_the_ID();
    $price = absint(get_post_meta($car_id, '_car_price', true));

    if ($price < 1) {
        return __('Contact for Price', 'car-showroom');
    }

    return '$' . number_format($price);
}

/**
 * Returns the car brand names as a comma-separated string.
 *
 * @param int|null $post_id Optional post ID.
 * @return string
 */
function shams_get_car_brand_list($post_id = null)
{
    $car_id = $post_id ? absint($post_id) : get_the_ID();
    $terms = get_the_terms($car_id, 'brand');

    if (empty($terms) || is_wp_error($terms)) {
        return __('Unbranded', 'car-showroom');
    }

    return implode(', ', wp_list_pluck($terms, 'name'));
}
