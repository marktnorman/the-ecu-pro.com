<?php global $the_cs_template_options; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> <?php echo (!empty($the_cs_template_options["general_cs_page_title"]) ? $the_cs_template_options["general_cs_page_title"] : 'Almost Ready to Launch | ' . get_bloginfo('name')); ?> </title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php igniteup_head(); ?>
    <style>
        a {
            color: <?php echo $the_cs_template_options['font_color'];
                    ?> !important;
            transition: all ease 400ms;
        }

        <?php if (!empty($the_cs_template_options['bg_image'])) : ?>body::after {
                content: '';
                background: url('<?php echo $the_cs_template_options['bg_image']; ?>') !important;
                opacity: 0.5;
                top: 0px;
                left: 0px;
                bottom: 0px;
                right: 0px;
                position: fixed;
                z-index: -1 !important;
                background-size: cover;
            }

            body {
                background: #000 !important;
            }

        <?php endif;
    ?>
    </style>
</head>

<body style="background: <?php echo $the_cs_template_options['bg_color']; ?>; color:<?php echo $the_cs_template_options['font_color']; ?> !important;">
    <div class="container-fluid main-container">
        <div class="row">
            <div class="col-sm-8">
                <div class='logo'><?php
                                    $logo = $the_cs_template_options['logo'];
                                    ?>
                    <img src="<?php echo $logo; ?>" class="img-responsive"></div>
                <div class="">
                    <p class="title-top">
                        <?php echo $the_cs_template_options['title_top']; ?>
                    </p>
                    <p class="title-bottom">
                        <?php echo $the_cs_template_options['main_title']; ?>
                    </p>
                    <p class="paragraph"><?php echo $the_cs_template_options['paragraph']; ?>
                    </p>
                </div>
                <ul class="social-icon">
                    <?php
                    foreach ($the_cs_template_options['social_icon_map'] as $key => $item) :
                        if (empty($the_cs_template_options[$key]))
                            continue;
                        echo '<li><a href="' . $the_cs_template_options[$key] . '" target="_blank"><span class = "fab fa-' . $item . '"></span></a></li>';
                    endforeach;    ?>
                </ul>
            </div>
            <div id="plane" class="hidden-xs <?php echo (($the_cs_template_options['plane']) == 1) ? '' : 'hidden' ?>">
                <img src="<?php echo plugins_url('images/plane.png', __FILE__); ?>" class="img-responsive">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <?php
                $powered_by = $the_cs_template_options['general_powered_by'];
                if ($powered_by == 1) {
                    $class = "visible";
                } else {
                    $class = "hidden";
                }
                ?>
                <div class="<?php echo $class; ?> text-center" id="powered-by">
                    Powered by <a href="https://wordpress.org/plugins/igniteup/" target="_blank">IgniteUp</a>
                </div>
            </div>
        </div>
    </div>
    <?php igniteup_footer(); ?>
</body>

</html>