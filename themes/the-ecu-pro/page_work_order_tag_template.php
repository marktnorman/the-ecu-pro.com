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

                                <div class="infoContainer hide"><em class="thankyouImage"><img
                                                src="https://static.zohocdn.com/forms/images/green-tick.d80571f0156757619a5040ba1bda4f1a.gif"></em>

                                    <span id="richTxtMsgSpan" class="infoCont"> <label class="descFld">
Thank you!<br><br><span class="colour" style="color: rgb(255, 51, 51)">Please write the following order number on your box with the parts that you send in:</span><br><br><span
                                                    class="size" style="font-size: 18.6667px"><span class="work-order-tag-div"></span><br><br><a
                                                        href="https://forms.zohopublic.com/theecupro/form/FRMRepairNeworder/publicrecord/z4DcfS5M8ALCfLp_QynD8-BUlBp0dT-0dxc_EquKig0"
                                                        title="Edit information provided" target="_blank"
                                                        rel="noopener noreferrer">Edit information provided</a><br></span><span
                                                    class="size" style="font-size: 48px"><br><br><br></span><div><br></div>
</label> </span></div>

                                <?php

                                if (have_posts()) {
                                    while (have_posts()) {
                                        the_post();
                                        the_content();
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
