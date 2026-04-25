<?php
get_header();

/**
 * Defines the query arguments for the homepage car grid.
 *
 * @return array<string, int|string>
 */
function shams_get_recent_cars_query_args()
{
    return array(
        'post_type'      => 'cars',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
    );
}

$shams_cars_query = new WP_Query(shams_get_recent_cars_query_args());
?>

<main class="container">
    <header class="showroom-header">
        <p class="showroom-kicker"><?php esc_html_e('Curated Inventory', 'car-showroom'); ?></p>
        <h1><?php esc_html_e('Latest Cars', 'car-showroom'); ?></h1>
        <p class="showroom-intro"><?php esc_html_e('Explore the newest vehicles added to the dealership collection.', 'car-showroom'); ?></p>
    </header>

    <?php if ($shams_cars_query->have_posts()) : ?>
        <section class="car-grid" aria-label="<?php esc_attr_e('Recent cars', 'car-showroom'); ?>">
            <?php while ($shams_cars_query->have_posts()) : ?>
                <?php
                $shams_cars_query->the_post();
                $shams_brand_list = shams_get_car_brand_list();
                $shams_price = shams_get_formatted_car_price();
                ?>
                <article <?php post_class('car-card'); ?>>
                    <a class="car-card__media" href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('car-thumbnail', array('class' => 'car-card__image')); ?>
                        <?php else : ?>
                            <div class="car-card__placeholder"><?php esc_html_e('No Image Available', 'car-showroom'); ?></div>
                        <?php endif; ?>
                    </a>

                    <div class="car-card__content">
                        <p class="car-card__brand"><?php echo esc_html($shams_brand_list); ?></p>
                        <h2 class="car-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <p class="car-card__price"><?php echo esc_html($shams_price); ?></p>
                        <a class="view-btn" href="<?php the_permalink(); ?>"><?php esc_html_e('View Details', 'car-showroom'); ?></a>
                    </div>
                </article>
            <?php endwhile; ?>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p class="car-grid__empty"><?php esc_html_e('No cars found in the collection.', 'car-showroom'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
