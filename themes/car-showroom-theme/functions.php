
<?php
function showroom_setup() {
    // This line enables the "Featured Image" box in the editor
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'showroom_setup');