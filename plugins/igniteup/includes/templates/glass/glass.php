<?php global $the_cs_template_options; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title><?php echo (!empty($the_cs_template_options["general_cs_page_title"]) ? $the_cs_template_options["general_cs_page_title"] : 'Almost Ready to Launch | ' . get_bloginfo('name')); ?></title>

	<?php igniteup_head(); ?>

	<style>
		.main-container a {
			color: <?php echo $the_cs_template_options['font_color'];
					?> !important;
			transition: all ease 400ms;
		}

		a:hover,
		a:focus {
			<?php echo 'color: ' . $the_cs_template_options['link_color'] . ' !important;' ?>
		}

		<?php if (!empty($the_cs_template_options['bg_image'])) : ?>body::after {
				background-image: url('<?php echo $the_cs_template_options['bg_image']; ?>');
			}

			body {
				background: #000 !important;
			}

		<?php endif;
	?>
	</style>

</head>

<body style=" color:<?php echo $the_cs_template_options['font_color']; ?>;">
	<div class="main-container">
		<div class="glass-wrapper">
			<?php if (!empty($the_cs_template_options['logo'])) : ?>
				<img class="img-responsive logo" src="<?php echo $the_cs_template_options['logo']; ?>">
			<?php endif; ?>
			<h2 class="text-center"> <?php echo $the_cs_template_options['title_top']; ?></h2>
			<p class="text-center"><?php echo $the_cs_template_options['paragraph']; ?></p>
			<div class="thankyou hidden" style="margin-bottom: -70px;">
				<div class="alert alert-success alert-dismissible" role="alert">
					<div class="text-center"> <?php echo CSAdminOptions::getDefaultStrings('alert_thankyou'); ?></div>
				</div>
			</div>
			<div class="error-msg hidden" style="margin-top: 0px;">
				<div class="alert alert-danger alert-dismissible" role="alert">
					<div class="text-center" id='error-msg-text'></div>
				</div>
			</div>
			<div class="row subscribe <?php echo (($the_cs_template_options['subscribe']) == 1) ? '' : 'hidden' ?>">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="subscribe input-group">
						<input type="email" id="cs_email" class="form-control" placeholder="<?php _e('Your email address', CSCS_TEXT_DOMAIN); ?>">
						<span class="input-group-btn">
							<button class="btn btn-default subscribe-btn" id="subscribe-btn" type="button"><?php echo CSAdminOptions::getDefaultStrings('subscribe_text'); ?></button>
						</span>
					</div>
				</div>
			</div>

			<ul class="social-icon">
				<?php
				foreach ($the_cs_template_options['social_icon_map'] as $key => $icon) :
					if (empty($the_cs_template_options[$key]))
						continue;
					echo '<li><a href="' . $the_cs_template_options[$key] . '"><span class="fab fa-' . $icon . '"></span></a></li>';
				endforeach;
				?>
			</ul>
			<?php
			$powered_by = $the_cs_template_options['general_powered_by'];
			($powered_by == 1) ? $class = "visible" : $class = "hidden";
			?>
			<div class="<?php echo $class; ?> text-center" id="powered-by">
				Powered by <a href="https://wordpress.org/plugins/igniteup/" target="_blank">IgniteUp</a>
			</div>
		</div>
	</div>
	<script src='<?php echo plugins_url('includes/js/jquery.min.js', CSCS_FILE) ?>' type="text/javascript"></script>
	<script>
		jQuery('.subscribe-btn').on('click', function() {
			subscribe();
		});
		jQuery('#cs_email').on('keypress', function(e) {
			if (e.which == 13) {
				subscribe();
			}
		});

		jQuery(function($) {
			function runPositioning() {
				var bheight = $(window).height();
				var cont_height = $('.main-container').height();
				var space = bheight - cont_height;
				if (space > 10) {
					$('.glass-wrapper').css('margin-top', space / 2);
					$('.glass-wrapper').css('margin-bottom', 0);
				}
			}
			runPositioning();
			$(window).on('resize', function() {
				runPositioning();
			});
		});

		function subscribe() {
			jQuery.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				data: {
					action: 'subscribe_email',
					cs_email: jQuery("#cs_email").val()
				},
				dataType: 'json',
				success: function(data) {
					if (data['error']) {
						jQuery('.error-msg #error-msg-text').html(data['message']);
						jQuery('.error-msg').removeClass('hidden');
						jQuery('.error-msg').addClass('animated fadeIn');

						function hideMsg() {
							jQuery('.error-msg').addClass('fadeOut');
						}
						setTimeout(hideMsg, 4000);

						function showMsg() {
							jQuery('.error-msg').addClass('hidden');
							jQuery('.error-msg').removeClass('animated fadeIn fadeOut');
						}
						setTimeout(showMsg, 4500);
					} else {
						jQuery('.subscribe').addClass('animated fadeOutDown');
						jQuery('.thankyou').removeClass('hidden');
						jQuery('.thankyou').addClass('animated fadeIn');
					}
				}
			});
		}
	</script>
	<?php igniteup_footer(); ?>
</body>

</html>