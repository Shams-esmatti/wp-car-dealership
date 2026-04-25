<?php get_header(); ?>

<div class="container">
    <header class="showroom-header">
        <h1>Car Collection</h1>
        <p>Premium showroom managed via custom WordPress architecture.</p>
    </header>

    <div class="car-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
        
        <?php
        // 1. The Query: Fetch only our 'cars' post type
        $args = array(
            'post_type' => 'cars',
            'posts_per_page' => 10,
        );
        $query = new WP_Query($args);

        // 2. The Loop
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post(); 
                // Get the custom price we saved in the plugin
                $price = get_post_meta(get_the_ID(), '_car_price', true);
            ?>

                <article class="car-card" style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div class="car-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium_large', array('style' => 'width:100%; height:200px; object-fit:cover;')); ?>
                        <?php else : ?>
                            <div style="height:200px; background:#ddd; display:flex; align-items:center; justify-content:center;">No Image</div>
                        <?php endif; ?>
                    </div>

                    <div class="car-content" style="padding: 20px;">
                        <h2 style="margin: 0 0 10px 0; font-size: 1.5rem;"><?php the_title(); ?></h2>
                        <p class="price" style="color: #2ecc71; font-weight: bold; font-size: 1.2rem;">
                            $<?php echo $price ? number_format($price) : 'Contact for Price'; ?>
                        </p>
                        <div style="margin: 15px 0; font-size: 0.9rem; color: #666;">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" style="display: inline-block; background: #333; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">View Details</a>
                    </div>
                </article>

            <?php endwhile;
            wp_reset_postdata(); // Professional move: Clean up the global post variable
        else :
            echo '<p>No cars found in the collection.</p>';
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>