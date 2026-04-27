<?php get_header(); ?>

<div class="container car-detail">
    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        $price = get_post_meta(get_the_ID(), '_car_price', true);
    ?>
        <div class="car-detail__layout">
            
            <div class="car-detail__content">
                <?php the_post_thumbnail('large', array('class' => 'car-detail__image')); ?>
                <div class="car-detail__copy">
                    <?php the_content(); ?>
                </div>
            </div>

            <aside class="car-detail__sidebar">
                <h1 class="car-detail__title"><?php the_title(); ?></h1>
                <p class="car-detail__price">$<?php echo number_format($price); ?></p>
                
                <hr class="car-detail__divider">

                <h3 class="car-detail__form-title">Place an Order</h3>
                <form class="car-detail__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <?php wp_nonce_field('shams_order_nonce', 'order_security'); ?>
                    <input type="hidden" name="action" value="shams_submit_order">
                    <input type="hidden" name="car_id" value="<?php the_ID(); ?>">

                    <div class="car-detail__field">
                        <input class="car-detail__input" type="text" name="user_name" placeholder="Full Name" required>
                    </div>
                    <div class="car-detail__field">
                        <input class="car-detail__input" type="email" name="user_email" placeholder="Email Address" required>
                    </div>
                    <button class="car-detail__submit" type="submit">
                        Confirm Interest
                    </button>
                </form>
            </aside>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
