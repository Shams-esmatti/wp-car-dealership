<?php
/**
 * Theme header.
 *
 * @package car-showroom
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="site-header__inner">
        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Car dealership home', 'car-showroom'); ?>">
            <img
                class="site-brand__logo"
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/logo-showroom.svg'); ?>"
                alt="<?php esc_attr_e('Auto showroom logo', 'car-showroom'); ?>"
                width="220"
                height="64"
            >
        </a>

        <nav class="site-header__nav" aria-label="<?php esc_attr_e('Primary navigation', 'car-showroom'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inventory', 'car-showroom'); ?></a>
        </nav>
    </div>
</header>
