<?php
/**
 * Template Name: Work order tag template
 */

get_header(); ?>

<div id="main" class="column1 boxed no-breadcrumbs"><!-- main -->

    <div class="outer-parent-container">
        <div class="container">
            <div class="row main-content-wrap">
                <div class="main-content col-lg-12">
                    <div class="woocommerce">
                        <div id="work-order-tag-container" class="woocommerce-order">
                            <div style="margin-top: 20px;">

                                <?php

                                if (isset($_GET) && !empty($_GET['work-order'])) { ?>
                                    <div class="infoContainer">
                                    <span id="richTxtMsgSpan" class="infoCont"> <label class="descFld">
Thank you!<br><br><span class="colour" style="color: rgb(255, 51, 51)">Please write the following order number on your box with the parts that you send in:</span><br><br><span
                                                    class="size" style="font-size: 18.6667px"><span
                                                        class="work-order-tag-div"><?php echo $_GET['work-order']; ?></span><br><br><br></span><span
                                                    class="size" style="font-size: 48px"><br><br><br></span><div><br></div>
</label> </span></div>
                                <?php } else {
                                    if (have_posts()) {
                                        while (have_posts()) {
                                            the_post();
                                            the_content();
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar">

                    </div>
                </div><!-- end outer-parent-container -->
            </div><!-- end container -->
        </div><!-- end main-content-wrap -->
    </div>

</div>

<?php get_footer(); ?>
