<div class="sidebar">
    <?php

    if (is_woocommerce() ) {
        dynamic_sidebar('woo-category-sidebar');
    } else {
        dynamic_sidebar();
    }

    ?>

</div>