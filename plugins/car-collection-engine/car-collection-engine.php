<?php
/**
 * Plugin Name: Car Collection Engine
 * Description: Core data structures for the Car Dealership project.
 * Version: 1.0.0
 * Author: Shams
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Returns the supported car meta fields and their sanitization callbacks.
 *
 * @return array<string, array<string, string>>
 */
function shams_get_car_meta_fields()
{
    return array(
        'car_price' => array(
            'label'             => 'Car Price ($)',
            'meta_key'          => '_car_price',
            'type'              => 'number',
            'sanitize_callback' => 'absint',
        ),
        'car_mileage' => array(
            'label'             => 'Mileage',
            'meta_key'          => '_car_mileage',
            'type'              => 'number',
            'sanitize_callback' => 'absint',
        ),
        'car_fuel_type' => array(
            'label'             => 'Fuel Type',
            'meta_key'          => '_car_fuel_type',
            'type'              => 'text',
            'sanitize_callback' => 'sanitize_text_field',
        ),
    );
}

/**
 * Registers the Cars custom post type.
 */
function shams_register_car_post_type()
{
    register_post_type(
        'cars',
        array(
            'labels' => array(
                'name'               => __('Cars', 'car-collection-engine'),
                'singular_name'      => __('Car', 'car-collection-engine'),
                'add_new'            => __('Add New', 'car-collection-engine'),
                'add_new_item'       => __('Add New Car', 'car-collection-engine'),
                'edit_item'          => __('Edit Car', 'car-collection-engine'),
                'new_item'           => __('New Car', 'car-collection-engine'),
                'view_item'          => __('View Car', 'car-collection-engine'),
                'search_items'       => __('Search Cars', 'car-collection-engine'),
                'not_found'          => __('No cars found.', 'car-collection-engine'),
                'not_found_in_trash' => __('No cars found in Trash.', 'car-collection-engine'),
                'menu_name'          => __('Cars', 'car-collection-engine'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'menu_icon'    => 'dashicons-performance',
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite'      => array('slug' => 'cars'),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'shams_register_car_post_type');

/**
 * Registers the hierarchical Brands taxonomy for cars.
 */
function shams_register_car_brand_taxonomy()
{
    register_taxonomy(
        'brand',
        array('cars'),
        array(
            'labels' => array(
                'name'              => __('Brands', 'car-collection-engine'),
                'singular_name'     => __('Brand', 'car-collection-engine'),
                'search_items'      => __('Search Brands', 'car-collection-engine'),
                'all_items'         => __('All Brands', 'car-collection-engine'),
                'parent_item'       => __('Parent Brand', 'car-collection-engine'),
                'parent_item_colon' => __('Parent Brand:', 'car-collection-engine'),
                'edit_item'         => __('Edit Brand', 'car-collection-engine'),
                'update_item'       => __('Update Brand', 'car-collection-engine'),
                'add_new_item'      => __('Add New Brand', 'car-collection-engine'),
                'new_item_name'     => __('New Brand Name', 'car-collection-engine'),
                'menu_name'         => __('Brands', 'car-collection-engine'),
            ),
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'brands'),
        )
    );
}
add_action('init', 'shams_register_car_brand_taxonomy');

/**
 * Registers the car details meta box for the Cars post type.
 */
function shams_add_car_meta_boxes()
{
    add_meta_box(
        'shams_car_details',
        __('Car Specifications', 'car-collection-engine'),
        'shams_render_car_meta_box',
        'cars',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'shams_add_car_meta_boxes');

/**
 * Renders the car details meta box fields with nonce protection.
 *
 * @param WP_Post $post Current post object.
 */
function shams_render_car_meta_box($post)
{
    $meta_fields = shams_get_car_meta_fields();

    wp_nonce_field('shams_save_car_meta', 'shams_car_meta_nonce');

    foreach ($meta_fields as $field_name => $field_config) {
        $value = get_post_meta($post->ID, $field_config['meta_key'], true);
        ?>
        <p>
            <label for="<?php echo esc_attr($field_name); ?>">
                <?php echo esc_html($field_config['label']); ?>
            </label>
            <input
                type="<?php echo esc_attr($field_config['type']); ?>"
                name="<?php echo esc_attr($field_name); ?>"
                id="<?php echo esc_attr($field_name); ?>"
                value="<?php echo esc_attr($value); ?>"
                class="widefat"
            >
        </p>
        <?php
    }
}

/**
 * Saves the car meta box fields after validating nonce and permissions.
 *
 * @param int $post_id Current post ID.
 */
function shams_save_car_meta($post_id)
{
    if (! isset($_POST['shams_car_meta_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['shams_car_meta_nonce'])), 'shams_save_car_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    foreach (shams_get_car_meta_fields() as $field_name => $field_config) {
        if (! isset($_POST[$field_name])) {
            continue;
        }

        $raw_value = wp_unslash($_POST[$field_name]);
        $value = call_user_func($field_config['sanitize_callback'], $raw_value);

        update_post_meta($post_id, $field_config['meta_key'], $value);
    }
}
add_action('save_post_cars', 'shams_save_car_meta');


// Handle the Form Submission
add_action('admin_post_nopriv_shams_submit_order', 'shams_process_order');
add_action('admin_post_shams_submit_order', 'shams_process_order');

/**
 * Processes the public order form and emails the dealership.
 */
function shams_process_order()
{
    if (
        ! isset($_POST['order_security']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['order_security'])), 'shams_order_nonce')
    ) {
        wp_die('Security check failed');
    }

    $car_id = isset($_POST['car_id']) ? absint($_POST['car_id']) : 0;
    $name = isset($_POST['user_name']) ? sanitize_text_field(wp_unslash($_POST['user_name'])) : '';
    $email = isset($_POST['user_email']) ? sanitize_email(wp_unslash($_POST['user_email'])) : '';

    if (! $car_id || '' === $name || ! is_email($email)) {
        wp_die('Please submit a valid order request.');
    }

    $car_title = get_the_title($car_id);
    $car_price = get_post_meta($car_id, '_car_price', true);
    $formatted_price = $car_price ? '$' . number_format((int) $car_price) : 'Contact for Price';
    $brand_terms = get_the_terms($car_id, 'brand');
    $brands = (! empty($brand_terms) && ! is_wp_error($brand_terms))
        ? implode(', ', wp_list_pluck($brand_terms, 'name'))
        : 'Unbranded';

    $recipient = get_option('admin_email');
    $subject = sprintf('New Car Order Request: %s', $car_title ? $car_title : 'Vehicle Inquiry');
    $message = implode(
        "\n",
        array(
            'A customer submitted a car order request.',
            '',
            'Customer name: ' . $name,
            'Customer email: ' . $email,
            'Car: ' . ($car_title ? $car_title : 'Unknown vehicle'),
            'Brand: ' . $brands,
            'Price: ' . $formatted_price,
            'Vehicle link: ' . get_permalink($car_id),
            '',
            'Submitted from: ' . home_url('/'),
        )
    );

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    wp_mail($recipient, $subject, $message, $headers);

    wp_redirect(home_url('/thank-you-order/'));
    exit;
}
