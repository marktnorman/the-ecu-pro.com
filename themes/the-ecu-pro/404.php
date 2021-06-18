<?php

get_header(); ?>

    <div id="content" class="no-content">
        <div class="container">
            <section class="page-not-found">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="page-not-found-main">
                            <h2 class="entry-title"><?php esc_html_e('404', 'porto'); ?> <i class="fas fa-file"></i>
                            </h2>
                            <p><?php _e(
                                    "We're sorry, but the page you were looking for doesn't exist.",
                                    'porto'
                                ); ?></p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <h4>Here are some useful links</h4>
                        <ul class="nav nav-list primary">
                            <li><a href="/">Home</a></li>
                            <li><a href="/contact-us">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>

<?php get_footer();
