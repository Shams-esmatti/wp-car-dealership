<?php get_header(); ?>

<div class="container" style="margin-top: 50px;">
    <?php if (have_posts()) : while (have_posts()) : the_post(); 
        $price = get_post_meta(get_the_ID(), '_car_price', true);
    ?>
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 50px;">
            
            <div>
                <?php the_post_thumbnail('large', array('style' => 'width:100%; border-radius:10px;')); ?>
                <div style="margin-top: 20px;">
                    <?php the_content(); ?>
                </div>
            </div>

            <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); height: fit-content;">
                <h1 style="margin-top:0;"><?php the_title(); ?></h1>
                <p style="font-size: 24px; color: #e74c3c; font-weight: bold;">$<?php echo number_format($price); ?></p>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

                <h3>Place an Order</h3>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <?php wp_nonce_field('shams_order_nonce', 'order_security'); ?>
                    <input type="hidden" name="action" value="shams_submit_order">
                    <input type="hidden" name="car_id" value="<?php the_ID(); ?>">

                    <div style="margin-bottom: 15px;">
                        <input type="text" name="user_name" placeholder="Full Name" required style="width:100%; padding:10px; border:1px solid #ddd;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <input type="email" name="user_email" placeholder="Email Address" required style="width:100%; padding:10px; border:1px solid #ddd;">
                    </div>
                    <button type="submit" style="width:100%; background: #2c3e50; color: white; padding: 15px; border: none; cursor: pointer; font-weight: bold;">
                        Confirm Interest
                    </button>
                </form>
            </div>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>