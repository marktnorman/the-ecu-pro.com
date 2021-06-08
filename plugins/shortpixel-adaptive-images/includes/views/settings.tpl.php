<?php
	/**
	 * @var \ShortPixelAI $controller
	 */
	$controller = $this->ctrl;
	$options    = $controller->options;

	$integrations = ShortPixel\AI\ActiveIntegrations::_( true );

	$status_box_integrations = [];

	foreach ( $integrations->getAll() as $integration => $value ) {
		if ( $integration === 'theme' ) {
			continue;
		}

		if ( is_bool( $value ) && !$value ) {
			continue;
		}
		else if ( is_array( $value ) && !!$value ) {
			$is_active = false;

			foreach ( $value as $item ) {
				if ( !!$item ) {
					$is_active = true;
					break;
				}
			}

			if ( $is_active ) {
				$status_box_integrations[] = ucwords( str_replace( [ '_', '-' ], ' ', $integration ) );
			}
		}
		else {
			$status_box_integrations[] = ucwords( str_replace( [ '_', '-' ], ' ', $integration ) );
		}
	}

	// SPAI API key
	$spai_key = $options->settings_general_apiKey;

	$domain_usage  = empty( $spai_key ) ? false : $this->ctrl->get_cdn_domain_usage( null, $spai_key );
	$domain_status = $this->ctrl->get_domain_status( true );
    $domain        = ShortPixelDomainTools::get_site_domain();

	$submit_button_attributes = [
		'data-saving-text' => __( 'Saving...', 'shortpixel-adaptive-images' ),
	];

	$css_status = $domain_status->HasAccount && $domain_status->Status === 2
		? 'enough'
		: ( $domain_status->Status === 1
			? 'few'
			: ( $domain_status->Status <= 0 ? 'insufficiently' : '' ) );

	$status_text = $domain_status->Status === 1
		? ( $domain_status->HasAccount ? __( 'Your account usage is close to the credits limit.', 'shortpixel-adaptive-images' ) : __( 'Your free credits are close to the limit.', 'shortpixel-adaptive-images' ) )
		: ( $domain_status->Status < 0
			? ( $domain_status->HasAccount ? __( 'You have used all your account\'s credits.', 'shortpixel-adaptive-images' ) : __( 'You have used all your free credits.', 'shortpixel-adaptive-images' ) )
			: '' );

	$tooltip_text = $domain_status->Status === 1
		? ( $domain_status->HasAccount ? __( 'You have less than 10% of your credits available.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please top up to ensure that you don\'t run out of credits.',
				'shortpixel-adaptive-images' ) : __( 'You have less than 10% of your credits available.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please create an account to get 1000 more free credits.',
				'shortpixel-adaptive-images' ) )
		: ( $domain_status->Status === -1
			? ( $domain_status->HasAccount ? __( 'Some of the image sizes and new images are no longer optimized.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please top up to ensure that you don\'t run out of credits.',
					'shortpixel-adaptive-images' ) : __( 'Some of the image sizes and new images are no longer optimized.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please create an account to get 1000 more free credits.',
					'shortpixel-adaptive-images' ) )
			: ( $domain_status->Status === -2
				? ( $domain_status->HasAccount ? __( 'Your images are no longer optimized.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please top up to ensure that you don\'t run out of credits.',
						'shortpixel-adaptive-images' ) : __( 'Your images are no longer optimized.', 'shortpixel-adaptive-images' ) . '<br>' . __( 'Please create an account to get 1000 more free credits.',
						'shortpixel-adaptive-images' ) )
				: '' ) );
?>
	<div class="wpf-settings">
		<div class="shortpixel-settings-wrap">
			<h1><?= esc_html( get_admin_page_title() ); ?> Settings</h1>
			<div class="support-and-preferences">
				<a href="https://help.shortpixel.com/category/307-shortpixel-adaptive-images" target="_blank"><?= __( 'Help', 'shortpixel-adaptive-images' ); ?></a>
				<a href="<?= ShortPixelAI::DEFAULT_MAIN_DOMAIN ?>/contact/" target="_blank"><?= __( 'Support', 'shortpixel-adaptive-images' ); ?></a>
			</div>
			<div class="spai_statusbox_wrap">
				<div class="title_wrap" data-status="<?= $css_status; ?>">
					<strong>
                        <?= $domain; ?>
                    </strong> <?=__('is', 'shortpixel-adaptive-images' ); ?>
                    <span class="<?= $domain_status->HasAccount ? 'success' : 'error'; ?>">
                        <?= $domain_status->HasAccount ? __( 'associated', 'shortpixel-adaptive-images' ) : __( 'not associated', 'shortpixel-adaptive-images' ); ?>
                    </span>
					<?= empty( $status_text ) ? ''
                        : '<div class="usage_msg">' . $status_text
                        . ' <span class="dashicons dashicons-editor-help" data-tippy-content="' . $tooltip_text . '" data-tippy-animation="shift-away" data-tippy-arrow="true"></span></div>'; ?>


                    <?php
                    if ($domain_status->Status === 1) {
                        if ( $spai_key != '' ) {
                            $login_link = ShortPixelAI::DEFAULT_MAIN_DOMAIN. "/loginai/" . $spai_key. "/". urlencode("{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
                        } else {
                            $login_link = ShortPixelAI::DEFAULT_MAIN_DOMAIN. "/loginai/" . urlencode("{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
                        }
                        ?>

                                    <a href="<?= $login_link; ?>" target="_blank" class="bordered_link btn_topup" data-tippy-content="<?= __( 'Top-up account', 'shortpixel-adaptive-images' ); ?>"
                                       data-tippy-animation="shift-away" data-tippy-placement="left" data-tippy-arrow="true"><?= __( 'Top-up', 'shortpixel-adaptive-images' ); ?></a><br>

                    <?php } ?>
				</div>
				<div class="box_content">
					<?php
						if ( $domain_status->HasAccount && $domain_usage ) {
						    if(!preg_match('/_CHILD_[0-9]+$/s', $domain_usage->email)) {
                                $login_link = ShortPixelAI::DEFAULT_MAIN_DOMAIN. "/loginai/" . $spai_key . "/dashboard";
                                ?>
                                <p class="clearfix">
                                    <a href="<?= $login_link; ?>" target="_blank" class="bordered_link login_btn" data-tippy-content="<?= __( 'Login into your ShortPixel account.', 'shortpixel-adaptive-images' ); ?>"
                                       data-tippy-animation="shift-away" data-tippy-placement="left" data-tippy-arrow="true"><?= __( 'Login', 'shortpixel-adaptive-images' ); ?></a>
                                    <?= __( 'Account', 'shortpixel-adaptive-images' ); ?>: <strong><a href="<?= $login_link; ?>"><?= $domain_usage->email; ?></a></strong>
                                </p>
                            <?php } ?>

							<button class="bordered_link login_btn" data-action="remove key"
                                    data-tippy-content="<?= __('Forget the API key for this domain', 'shortpixel-adaptive-images' ); ?>. <?= __('The plugin will continue work, but you won\'t see the stats below.', 'shortpixel-adaptive-images' ); ?>"
                                    data-tippy-animation="shift-away" data-tippy-placement="left"
                                    data-tippy-arrow="true"><?= __( 'Forget', 'shortpixel-adaptive-images' ); ?></button>

							<p><?= __( 'Total monthly credits', 'shortpixel-adaptive-images' ) . ': <strong>' . $domain_usage->quota->monthly->total . '</strong>'; ?></p>
							<div class="progress_wrap">
								<div class="available"><?= $domain_usage->quota->monthly->available . ' ' . __( 'available', 'shortpixel-adaptive-images' ); ?></div>
								<div class="used"><?= $domain_usage->quota->monthly->used . ' ' . __( 'used', 'shortpixel-adaptive-images' ); ?></div>
								<div class="progress">
									<div class="used" style="width: <?= $domain_usage->quota->monthly->usedPercent; ?>%"></div>
								</div>
							</div>
							<p><?= __( 'Total one-time credits', 'shortpixel-adaptive-images' ) . ': <strong>' . $domain_usage->quota->oneTime->total . '</strong>'; ?></p>
							<div class="progress_wrap">
								<div class="available"><?= $domain_usage->quota->oneTime->available . ' ' . __( 'available', 'shortpixel-adaptive-images' ); ?></div>
								<div class="used"><?= $domain_usage->quota->oneTime->used . ' ' . __( 'used', 'shortpixel-adaptive-images' ); ?></div>
								<div class="progress">
									<div class="used" style="width: <?= $domain_usage->quota->oneTime->usedPercent; ?>%"></div>
								</div>
							</div>
							<div class="chart-wrap">
								<div class="toggle"></div>
								<p>Daily:</p>
								<canvas id="chart"></canvas>
							</div>
							<p><?= __( 'CDN traffic', 'shortpixel-adaptive-images' ) . ': <strong>' . round( $domain_usage->cdn->total / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . '</strong>'; ?></p>
							<div class="progress_wrap">
								<div class="available"><?= round( $domain_usage->cdn->available / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . ' ' . __( 'available', 'shortpixel-adaptive-images' ); ?></div>
								<div class="used"><?= round( $domain_usage->cdn->used / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . ' ' . __( 'used', 'shortpixel-adaptive-images' ); ?></div>
								<div class="progress">
									<div class="used" style="width: <?= $domain_usage->cdn->usedPercent; ?>%"></div>
								</div>
							</div>
							<?php
						}
						else if ( !$domain_status->HasAccount && $domain_status->FreeCredits) {
							?>
							<div class="buttons-wrap">
								<a href="<?= ShortPixelAI::DEFAULT_MAIN_DOMAIN ?>/free-sign-up" target="_blank" class="dark_blue_link"><?= __( 'Create account', 'shortpixel-adaptive-images' ); ?></a>
								<a href="<?= ShortPixelAI::DEFAULT_MAIN_DOMAIN ?>/loginai" target="_blank" class="bordered_link"><?= __( 'Associate existing', 'shortpixel-adaptive-images' ); ?></a>
							</div>
							<p><?= __( 'Total credits', 'shortpixel-adaptive-images' ) . ': <strong>' . round($domain_status->FreeCredits) . '</strong>'; ?></p>
							<div class="progress_wrap">
								<?php
									$domain_status->UsedFreeCredits = $domain_status->UsedFreeCredits > $domain_status->FreeCredits ? $domain_status->FreeCredits : $domain_status->UsedFreeCredits;

									$used_percent      = round( 100 * $domain_status->UsedFreeCredits / $domain_status->FreeCredits, 0 );
									$available_percent = 100 - $used_percent;
								?>
								<div class="available"><?= $domain_status->FreeCredits - $domain_status->UsedFreeCredits . ' ' . __( 'available', 'shortpixel-adaptive-images' ); ?></div>
								<div class="used"><?= $domain_status->UsedFreeCredits . ' ' . __( 'used', 'shortpixel-adaptive-images' ); ?></div>
								<div class="progress">
									<div class="used" style="width: <?= $used_percent; ?>%"></div>
								</div>
							</div>
							<p><?= __( 'CDN traffic', 'shortpixel-adaptive-images' ) . ': <strong>' . round( $domain_status->CDNQuota / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . '</strong>'; ?></p>
							<div class="progress_wrap">
								<?php
									$used_percent      = round( $domain_status->UsedCDN / ( $domain_status->CDNQuota / 100 ), 2 );
									$available_percent = 100 - $used_percent;
								?>
								<div class="available"><?= round( ( $domain_status->CDNQuota - $domain_status->UsedCDN ) / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . ' ' . __( 'available', 'shortpixel-adaptive-images' ); ?></div>
								<div class="used"><?= round( $domain_status->UsedCDN / 1024 / 1024 / 1024, 2 ) . __( 'Gb', 'shortpixel-adaptive-images' ) . ' ' . __( 'used', 'shortpixel-adaptive-images' ); ?></div>
								<div class="progress">
									<div class="used" style="width: <?= $used_percent; ?>%"></div>
								</div>
							</div>
							<a href="<?= ShortPixelAI::DEFAULT_MAIN_DOMAIN. "/loginai/" . $spai_key ?>" class="dark_blue_link full_width">Top-up now</a>
							<?php
						}
						else {
							$api_key = $options->settings_general_apiKey;
							?>
							<div class="buttons-wrap">
								<form id="api-key-form" method="post" action="<?= admin_url( 'admin-ajax.php' ); ?>">
									<input type="hidden" name="action" value="shortpixel_ai_handle_page_action" />
									<input type="hidden" name="page" value="settings" />
									<input type="hidden" name="data[action]" value="save key" />

									<label for="api_key"><?= __( 'Enter your account\'s API key to get detailed info', 'shortpixel-adaptive-images' ) . ':'; ?></label>
									<input id="api_key" class="full_width" type="text" name="data[api_key]" size="25" value="<?= $api_key; ?>" autocomplete="off"/>
									<button type="submit" class="dark_blue_link full_width" data-saving-text="<?= __( 'Saving...', 'shortpixel-adaptive-images' ); ?>"><?= __( 'Save', 'shortpixel-adaptive-images' ); ?></button>
								</form>
							</div>
							<?php
						}
					?>
					<div class="box_dropdown first">
						<div class="title"><?= __( 'Active integrations', 'shortpixel-adaptive-images' ); ?></div>
						<div class="dropdown_content">
							<?php
								echo '<p><strong>' . __( 'Theme:', 'shortpixel-adaptive-images' ) . '</strong> ' . $integrations->get( 'theme' ) . '</p>';

								foreach ( $status_box_integrations as $index => $integration ) {
									echo '<p><strong>' . ( $index + 1 ) . '.</strong> ' . $integration . '</p>';
								}
							?>
						</div>
					</div>
					<div class="box_dropdown">
						<div class="title"><?= __( 'Dismissed notifications', 'shortpixel-adaptive-images' ); ?></div>
						<div class="dropdown_content">
							<?php
								$dismissed = $options->notices_dismissed;

								if ( !empty( (array) $dismissed ) ) {
									foreach ( $dismissed as $key => $item ) {
										$message = $this->noticeConstants->{$key};
										?>
										<div class="dismissed-notice-wrap" data-key="<?= ucwords( str_replace( [ '_', '-' ], ' ', $key ) ); ?>">
											<h4><?= $message[ 'title' ]; ?></h4>
											<?php
												foreach ( $message[ 'body' ] as $paragraph ) {
													echo '<p>' . $paragraph . '</p>';
												}
											?>
										</div>
									<?php }
								}
								else {
									echo '<p>' . __( 'No dismissed notifications.', 'shortpixel-adaptive-images' ) . '</p>';
								} ?>
						</div>
					</div>
				</div>

                <div class="shortpixel-offer-wso">
                    <p class="img-wrapper"><img src="<?= $controller->plugin_url . 'assets/img/shortpixel.png'; ?>" alt="ShortPixel"></p>
                    <h3>ARE YOU <br> CONCERNED WITH <br> YOUR <br> <span class="red"> SITE SPEED? </span><br><br>
                        ALLOW ShortPixel <br> SPECIALISTS TO <br> FIND THE <br> SOLUTION FOR YOU.</h3>
                    <p class="button-wrapper"><a href="https://wso.shortpixel.com/?utm_source=SPAI" target="_blank">FIND OUT MORE</a></p>
                </div>
                <script>if(window.location.hash && window.location.hash.indexOf('compression') < 0) document.getElementsByClassName('shortpixel-offer-wso')[0].style.display = 'block';</script>

            </div>
			<div class="shortpixel-settings-tabs">
				<h2 class="nav-tab-wrapper" id="wpspai-tabs">
					<a class="nav-tab nav-tab-active" id="compression-tab" href="#top#compression"><?= __( 'Compression', 'shortpixel-adaptive-images' ); ?></a>
					<a class="nav-tab" id="behaviour-tab" href="#top#behaviour"><?= __( 'Behaviour', 'shortpixel-adaptive-images' ); ?></a>
					<a class="nav-tab" id="areas-tab" href="#top#areas"><?= __( 'Areas', 'shortpixel-adaptive-images' ); ?></a>
					<a class="nav-tab" id="exclusions-tab" href="#top#exclusions"><?= __( 'Exclusions', 'shortpixel-adaptive-images' ); ?></a>
					<button
						id="clear_css_cache" data-pressed-text="<?= __( 'Clearing...', 'shortpixel-adaptive-images' ); ?>"
					><?= __( 'Clear CSS Cache', 'shortpixel-adaptive-images' ); ?></button>
				</h2>
				<form id="settings-form" method="post" action="<?= admin_url( 'admin-ajax.php' ); ?>">
					<div id="compression" class="spai_settings_tab active">
						<table class="form-table">
							<tr>
								<th scope="row"><?= __( 'Compression Level', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<?php
										$compression_level = $options->get( 'level', [ 'settings', 'compression' ], 'lossy' );
									?>
									<div class="shortpixel_radio_btns">
										<input id="lossy" type="radio" name="level" data-type="string" value="lossy" <?php checked( 'lossy', $compression_level, true ); ?>>
										<label for="lossy" title="<?= __( 'This is the recommended option in most cases, producing results that look the same as the original to the human eye.', 'shortpixel-adaptive-images' ); ?>">
											<span>Lossy</span>
										</label>
										<input id="glossy" type="radio" name="level" data-type="string" value="glossy" <?php checked( 'glossy', $compression_level, true ); ?>>
										<label for="glossy" title="<?= __( 'Best option for photographers and other professionals that use very high quality images on their sites and want best compression while keeping the quality untouched.', 'shortpixel-adaptive-images' ); ?>">
											<span>Glossy</span>
										</label>
										<input id="lossless" type="radio" name="level" data-type="string" value="lossless" <?php checked( 'lossless', $compression_level, true ); ?>>
										<label
											for="lossless"
											title="<?= __( 'Make sure not a single pixel looks different in the optimized image compared with the original. In some rare cases you will need to use this type of compression. Some technical drawings or images from vector graphics are possible situations.' ); ?>"
										>
											<span>Lossless</span>
										</label>
									</div>
									<?= __( '<a href="https://shortpixel.com/online-image-compression" target="_blank">Make a few tests</a> to help you decide.', 'shortpixel-adaptive-images' ); ?>
									<p class="<?= $compression_level === 'lossy' ? '' : 'hidden'; ?>" data-explanation="lossy"><?=
											__( '<b>Lossy compression (recommended): </b>offers the best compression rate.</br> This is the recommended option for most users, producing results that look the same as the original to the human eye.',
												'shortpixel-adaptive-images' ); ?></p>
									<p class="<?= $compression_level === 'glossy' ? '' : 'hidden'; ?>" data-explanation="glossy"><?=
											__( '<b>Glossy compression: </b>creates images that are almost pixel-perfect identical to the originals.</br> Best option for photographers and other professionals that use very high quality images on their sites and want best compression while keeping the quality untouched.',
												'shortpixel-adaptive-images' ); ?></p>
									<p class="<?= $compression_level === 'lossless' ? '' : 'hidden'; ?>" data-explanation="lossless"><?=
											__( '<b>Lossless compression: </b> the resulting image is pixel-identical with the original image.</br>Make sure not a single pixel looks different in the optimized image compared with the original.
										In some rare cases you will need to use this type of compression. Some technical drawings or images from vector graphics are possible situations.', 'shortpixel-adaptive-images' ); ?></p>
								</td>
							</tr>
							<tr>
								<?php
									$webp_support = $options->settings_compression_webp && ( $options->settings_compression_pngToWebp && $options->settings_compression_jpgToWebp && $options->settings_compression_gifToWebp );

									if ( !$webp_support ) {
										$options->settings_compression_webp = false;
									}
								?>

								<th scope="row"><?= __( 'WebP Support', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="webp"
										type="checkbox"
										name="webp"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( 1, $webp_support, true ); ?>/>
									<label for="webp" class="tgl-btn">
										<span></span>
										<?= __( 'Serve the images in the next-gen WebP image format to all the browsers that <a href="https://caniuse.com/#search=webp" target="_blank">support</a> it.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<div class="children-wrap<?= $webp_support ? '' : ' hidden' ?>" data-depended="webp">
										<input
											id="png_to_webp"
											type="checkbox"
											name="png_to_webp"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_compression_pngToWebp, true ); ?>
										/>
										<label for="png_to_webp" class="tgl-btn">
											<span></span>
											<?= sprintf( __( 'Serve <strong>%s</strong> images in the WebP format', 'shortpixel-adaptive-images' ), 'PNG' ); ?>
										</label>
									</div>
									<div class="children-wrap<?= $webp_support ? '' : ' hidden' ?>" data-depended="webp">
										<input
											id="jpg_to_webp"
											type="checkbox"
											name="jpg_to_webp"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_compression_jpgToWebp, true ); ?>
										/>
										<label for="jpg_to_webp" class="tgl-btn">
											<span></span>
											<?= sprintf( __( 'Serve <strong>%s</strong> images in the WebP format', 'shortpixel-adaptive-images' ), 'JPG' ); ?>
										</label>
									</div>
									<div class="children-wrap<?= $webp_support ? '' : ' hidden' ?>" data-depended="webp">
										<input
											id="gif_to_webp"
											type="checkbox"
											name="gif_to_webp"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_compression_gifToWebp, true ); ?>
										/>
										<label for="gif_to_webp" class="tgl-btn">
											<span></span>
											<?= sprintf( __( 'Serve <strong>%s</strong> images in the WebP format', 'shortpixel-adaptive-images' ), 'GIF' ); ?>
										</label>
									</div>
									<p class="description">
										<?= __( 'The conversion and optimization from the original image format to WebP will be done one-the-fly by ShortPixel. Recommended for SEO.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Remove EXIF', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="remove_exif"
										type="checkbox"
										name="remove_exif"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( 1, $options->settings_compression_removeExif, true ); ?>/>
									<label for="remove_exif" class="tgl-btn">
										<span></span>
										<?= __( 'Remove the EXIF info from the images.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'The images will be smaller and no information about author/location will be present in the image. <a href="https://blog.shortpixel.com/how-much-smaller-can-be-images-without-exif-icc/" target="_blank">Read more</a>',
											'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
						</table>
					</div>
					<div id="behaviour" class="spai_settings_tab">
						<table class="form-table">
							<tr>
								<th scope="row"><?= __( 'Fade-in effect', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="fadein"
										type="checkbox"
										name="fadein"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( 1, $options->settings_behaviour_fadein, true ); ?>/>
									<label for="fadein" class="tgl-btn">
										<span></span>
										<?= __( 'Fade-in the lazy-loaded images.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'If you experience problems with images that zoom on hover or have other special effects, try deactivating this option.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Smart crop', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="crop"
										type="checkbox"
										name="crop"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( 1, $options->settings_behaviour_crop, true ); ?>/>
									<label for="crop" class="tgl-btn">
										<span></span>
										<?= __( 'Smartly crop the images when possible and safe.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'The plugin will identify cases when not all the image is displayed and crop it accordingly. This might not work for some backgrounds (won\'t harm them though).', 'shortpixel-adaptive-images' ); ?>
									</p>
									<div class="notification_popup hidden">
										<p class="text"><?= __( 'In some rare cases this option can shift the backgrounds with respect to the uncropped ones. Please check your site after activating this option. If you notice any issue, you can always revert by deactivating it.',
												'shortpixel-adaptive-images' ); ?></p>
										<input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Replace method', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<?php
										$replace_method = $options->get( 'replace_method', [ 'settings', 'behaviour' ], 'src' );
									?>
									<div class="shortpixel_radio_btns">
										<input id="src" type="radio" name="replace_method" data-type="string" value="src" <?php checked( 'src', $replace_method, true ); ?>>
										<label for="src" title="<?= __( 'SRC makes sure as many images as possible are used with best fit.', 'shortpixel-adaptive-images' ); ?>">
											<span>SRC</span>
										</label>
										<input id="both" type="radio" name="replace_method" data-type="string" value="both" <?php checked( 'both', $replace_method, true ); ?>>
										<label for="both" title="<?= __( 'EXPERIMENTAL: Use BOTH if you have images that dynamically change size (enlarge on hover, etc.)', 'shortpixel-adaptive-images' ); ?>">
											<span>BOTH</span>
										</label>
										<input id="srcset" type="radio" name="replace_method" data-type="string" value="srcset" <?php checked( 'srcset', $replace_method, true ); ?>>
										<label for="srcset" title="<?= __( 'EXPERIMENTAL: Use SRCSET if you still encounter problems with specific content.', 'shortpixel-adaptive-images' ); ?>">
											<span>SRCSET</span>
										</label>
									</div>
									<p class="description <?= $replace_method === 'src' ? '' : 'hidden'; ?>" data-explanation="src"><?= __( 'SRC makes sure as many images as possible are used with best fit.', 'shortpixel-adaptive-images' ); ?></p>
									<p class="description <?= $replace_method === 'both' ? '' : 'hidden'; ?>" data-explanation="both"><?= __( 'EXPERIMENTAL: Use BOTH if you have images that dynamically change size (enlarge on hover, etc.)', 'shortpixel-adaptive-images' ); ?></p>
									<p class="description <?= $replace_method === 'srcset' ? '' : 'hidden'; ?>" data-explanation="srcset"><?= __( 'EXPERIMENTAL: Use SRCSET if you still encounter problems with specific content.', 'shortpixel-adaptive-images' ); ?></p>
									<div class="children-wrap<?= $replace_method === 'src' ? '' : ' hidden' ?>" data-parent="src">
										<input
											id="generate_noscript"
											type="checkbox"
											name="generate_noscript"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_behaviour_generateNoscript, true ); ?>
										/>
										<label for="generate_noscript" class="tgl-btn">
											<span></span>
											<?= sprintf( __( 'Generate %s tag', 'shortpixel-adaptive-images' ), esc_html( '<noscript>' ) ); ?>
										</label>
										<p class="description"><?= __( 'Generate the fallback html for the browsers that do not support JavaScript.', 'shortpixel-adaptive-images' ); ?></p>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'API URL', 'shortpixel-adaptive-images' ); ?></th>
								<td><label>
										<input
											type="text"
											name="api_url"
											data-type="string"
											size="40"
											placeholder="<?= ShortPixelAI::DEFAULT_API_AI . ShortPixelAI::DEFAULT_API_AI_PATH; ?>"
											value="<?= $options->settings_behaviour_apiUrl; ?>"
										/>
									</label>
									<p class="description">
										<?= __( 'Do <strong>not</strong> change this unless you plan on using your own CDN and you have it already configured to use ShortPixel.ai service. Check out <a href="https://shortpixel.helpscoutdocs.com/article/180-can-i-use-a-different-cdn-with-shortpixel-adaptive-images" target="_blank">here</a> or <a href="https://shortpixel.helpscoutdocs.com/article/200-setup-your-stackpath-account-so-that-it-can-work-with-shortpixel-adaptive-images-api" target="_blank">here</a> for examples',
											'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?= __( 'Lazy-load threshold', 'shortpixel-adaptive-images' ); ?>
								</th>
								<td>
									<?php
										$lazy_threshold = $options->get( 'lazy_threshold', [ 'settings', 'behaviour', 500 ] );
										$lazy_threshold = is_int( $lazy_threshold ) && $lazy_threshold >= 0 ? $lazy_threshold : 500;
									?>

									<label><input
											type="number"
											name="lazy_threshold"
											data-type="int"
											min="0"
											max="10000"
											value="<?= $lazy_threshold; ?>"
										/> px.</label>
									<p class="description">
										<?= __( 'The lazy-load threshold identifies how early before entering the visible area of the page (viewport) a lazy image is loaded. For example, the default of 500px means that an image is loaded when it\'s closer than 500px to the viewport.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?= __( 'Images hover handling', 'shortpixel-adaptive-images' ); ?>
								</th>
								<td>
									<input
										id="hover_handling"
										type="checkbox"
										name="hover_handling"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_behaviour_hoverHandling, true ); ?>>
									<label for="hover_handling" class="tgl-btn">
										<span></span>
										<?= __( 'Handle the on hover swapping images.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'Handle the swapping images which might be changed when hovering on one of them.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
                            <tr>
                                <th scope="row">
                                    <?= __( 'Logged-in users', 'shortpixel-adaptive-images' ); ?>
                                </th>
                                <td>
                                    <input
                                            id="replace_logged_in"
                                            type="checkbox"
                                            name="replace_logged_in"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->get( 'replace_logged_in', [ 'settings', 'behaviour' ], true ), true ); ?>>
                                    <label for="replace_logged_in" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Optimize the images for logged-in users.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Deactivate this option if the image optimization conflicts with your page builder. Please note that in this case you will no longer be able to use the <a href="https://help.shortpixel.com/article/338-how-to-use-the-image-checker-tool" target="_blank">Image Checker Tool</a>.', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                </td>
                            </tr>
							<tr>
								<?php
									$lqip        = !!$options->settings_behaviour_lqip;
									$process_way = $options->settings_behaviour_processWay;
									$process_way = empty( $process_way ) ? 'cron' : $process_way;
								?>
								<th scope="row">
									<?= __( 'LQ image placeholders', 'shortpixel-adaptive-images' ); ?>
								</th>
								<td>
									<input
										id="lqip"
										type="checkbox"
										name="lqip"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $lqip, true ); ?>>
									<label for="lqip" class="tgl-btn">
										<span></span>
										<?= __( 'Create and use inline Low Quality Image Placeholders (LQIPs) instead of blank placeholders.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'The LQIPs will be created after the first display of that particular image and will be stored in a cache in the Uploads folder.', 'shortpixel-adaptive-images' ); ?>
									</p>
                                    <div class="notification_popup hidden">
                                        <p class="text"><?= __( 'Please delete all levels of cache after activating this option.',
                                                'shortpixel-adaptive-images' ); ?></p>
                                        <input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
                                    </div>
									<div class="children-wrap<?= $lqip ? '' : ' hidden' ?>" data-parent="lqip">
										<div class="shortpixel_radio_btns">
											<input id="cron" type="radio" name="process_way" data-type="string" value="cron" <?php checked( 'cron', $process_way, true ); ?>>
											<label for="cron" title="<?= __( 'Use WP Cron', 'shortpixel-adaptive-images' ); ?>">
												<span>CRON</span>
											</label>
											<input id="instant" type="radio" name="process_way" data-type="string" value="instant" <?php checked( 'instant', $process_way, true ); ?>>
											<label for="instant" title="<?= __( 'Use AJAX calls', 'shortpixel-adaptive-images' ); ?>">
												<span>INSTANT</span>
											</label>
										</div>
										<p class="description <?= $process_way === 'cron' ? '' : 'hidden'; ?>" data-explanation="cron"><?= __( 'Use WP Cron to create of the LQIPs asynchronously and reduce the number of JavaScript calls.', 'shortpixel-adaptive-images' ); ?></p>
										<p class="description <?= $process_way === 'instant' ? '' : 'hidden'; ?>" data-explanation="instant"><?= __( 'EXPERIMENTAL: Use this if you have WP Cron deactivated.',
												'shortpixel-adaptive-images' ); ?></p>
									</div>

                                    <?php if(false) { //TODO remove completely ?>
									<div class="children-wrap<?= $lqip ? '' : ' hidden' ?>" data-parent="lqip">
										<input
											id="local_storage"
											type="checkbox"
											name="local_storage"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_behaviour_localStorage, true ); ?>
										/>
										<label for="local_storage" class="tgl-btn">
											<span></span>
											<?= __( 'Use browser\'s local storage', 'shortpixel-adaptive-images' ); ?>
										</label>
										<p class="description"><?= __( 'Use browser\'s local storage to store the already sent/processed URLs to decrease load on server.', 'shortpixel-adaptive-images' ); ?></p>
									</div>
                                    <?php } ?>

									<div class="children-wrap<?= $lqip ? '' : ' hidden' ?>" data-parent="lqip">
										<button class="bordered_link" type="button" data-action="clear lqip cache"><?= __( 'Clear LQIP cache', 'shortpixel-adaptive-images' ); ?></button>
										<p class="description"><?= __( 'If you have replaced some of the images but kept the same name, please clear the cache.', 'shortpixel-adaptive-images' ); ?></p>
									</div>
								</td>
							</tr>
                            <tr>
                                <th scope="row"><?= __( 'Native lazy-loading', 'shortpixel-adaptive-images' ); ?></th>
                                <td><input
                                            id="native_lazy"
                                            type="checkbox"
                                            name="native_lazy"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->settings_behaviour_nativeLazy, true ); ?>/>
                                    <label for="native_lazy" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Use browser native lazy-loading if the browser supports it.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Use the browser\'s native lazy-loading capabilities. Not recommended because most browsers have huge thresholds (Chrome has 8000px). Currently Firefox, Chrome and Edge are supporting native lazy-loading, among others. Most notable exception is Safari.', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?= __( 'Alter width and height', 'shortpixel-adaptive-images' ); ?>
                                </th>
                                <td>
                                    <input
                                            id="alter2wh"
                                            type="checkbox"
                                            name="alter2wh"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->settings_behaviour_alter2wh, true ); ?>>
                                    <label for="alter2wh" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Modify the width and height attributes of the IMG tags to reflect the resized image size.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Activate this if you have screen size issues with images that are handled by JavaScript after they are resized by ShortPixel', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?= __( 'Sizes from postmeta', 'shortpixel-adaptive-images' ); ?>
                                </th>
                                <td>
                                    <input
                                            id="sizespostmeta"
                                            type="checkbox"
                                            name="sizespostmeta"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->settings_behaviour_sizespostmeta, true ); ?>>
                                    <label for="sizespostmeta" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Check the postmeta table for image sizes, if the image is not present locally.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Activate this if you have offloaded images and you experience layout shifts while the page is rendered in the browser. Check the database load afterwards, if you have a very big postmeta table.', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <?php if(false) { //Will be included in 2.2 ?>
                            <tr>
                                <th scope="row">
                                    <?= __( 'No jQuery', 'shortpixel-adaptive-images' ); ?>
                                </th>
                                <td>
                                    <input
                                            id="nojquery"
                                            type="checkbox"
                                            name="nojquery"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->settings_behaviour_nojquery, true ); ?>>
                                    <label for="nojquery" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Use the new no-jQuery javascript.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Use the new javascript that doesn\'t need jQuery and is better optimized for new browsers.', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                    <div class="notification_popup hidden">
                                        <p class="text"><?= __( 'Please check your site after activating this option, especially on older browsers. If you notice any issue, you can always revert by deactivating it.',
                                                'shortpixel-adaptive-images' ); ?></p>
                                        <input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
                                    </div>

                                </td>
                            </tr>
                            <?php } ?>
						</table>
					</div>
					<div id="areas" class="spai_settings_tab">
						<table class="form-table">
							<tr>
								<th scope="row"><?= __( 'Serve SVGs through CDN', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="serve_svg"
										type="checkbox"
										name="serve_svg"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_serveSvg, true ); ?>
									/>
									<label for="serve_svg" class="tgl-btn">
										<span></span>
										<?= __( 'Serve the SVG images through the ShortPixel CDN', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'SVG images will be provided by our CDN without any changes.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Lazy-load the backgrounds', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="backgrounds_lazy_style"
										type="checkbox"
										name="backgrounds_lazy_style"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_backgroundsLazyStyle, true ); ?>
									/>
									<label for="backgrounds_lazy_style" class="tgl-btn">
										<span></span>
										<?= __( 'Lazy-load the background images from inline STYLE blocks.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'This will make the backgrounds in inline STYLE blocks be loaded after the device WebP capability and width are determined. Will also impose a maximum width of the backgrounds equal to the viewport width.', 'shortpixel-adaptive-images' ); ?>
									</p>
									<input
										id="backgrounds_lazy"
										type="checkbox"
										name="backgrounds_lazy"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_backgroundsLazy, true ); ?>
									/>
									<label for="backgrounds_lazy" class="tgl-btn">
										<span></span>
										<?= __( 'Lazy-load and resize the background images in TAGS inline styles.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<p class="description">
										<?= __( 'This will make the backgrounds defined in the STYLE attribute of the tags be loaded after the device WebP and width are determined. Will also impose a maximum width of the backgrounds equal to the viewport width.', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Backgrounds maximum width', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<?php
										$backgrounds_width = $options->settings_areas_backgroundsMaxWidth;
										$backgrounds_width = is_int( $backgrounds_width ) && $backgrounds_width >= 0 ? $backgrounds_width : 1920;
									?>
									<label><input
											type="number"
											name="backgrounds_max_width"
											data-type="int"
											min="0"
											max="10000"
											value="<?= $backgrounds_width; ?>"
										/> px.</label>
									<p class="description">
										<?= __( 'Maximum width of the backgrounds, on all devices. Use to scale down huge backgrounds that are not lazy-loaded. Recommended value is 1920px', 'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Replace in CSS files', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="parse_css_files"
										type="checkbox" name="parse_css_files"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_parseCssFiles, true ); ?>
									/>
									<label for="parse_css_files" class="tgl-btn">
										<span></span>
										<?php
                                            $labelText = __( 'Replace background images in the CSS cached by %s.', 'shortpixel-adaptive-images' );
											if ( $integrations->has( 'wp-rocket', 'minify-css') && $integrations->has( 'wp-rocket', 'css-filter' ) ) {
												echo sprintf($labelText , 'WP Rocket' ) . '<br>';
											}
											else if ( $integrations->has( 'swift-performance', 'merge_styles'  )
												&& $integrations->get ( 'swift-performance',  'plugin' )  === 'pro'
											) {
												echo sprintf( $labelText, 'Swift Performance ' . ucwords( $integrations->has( 'swift-performance', 'plugin' ) ) ) . '<br>';
											}
											else if ( $integrations->has( 'wp-fastest-cache' ) ) {
												echo sprintf( $labelText, 'WP Fastest Cache' ) . '<br>';
											}
											else if ( $integrations->has( 'w3-total-cache' ) ) {
												echo sprintf( $labelText, 'W3 Total Cache' ) . '<br>';
											}
                                            else if ( $integrations->has( 'litespeed-cache' ) ) {
                                                echo sprintf( $labelText, 'LiteSpeed Cache' ) . '<br>';
                                            }
											else {
												_e( 'Minify the CSS, replace background image URLs and serve the CSS files from the CDN.', 'shortpixel-adaptive-images' );
												?>
											<?php } ?>
									</label>
									<?php if ( ( !$integrations->has('wp-rocket', 'minify-css') || !$integrations->has('wp-rocket', 'css-filter' ) )
                                              && !$integrations->has('wp-fastest-cache')
                                              && !$integrations->has( 'w3-total-cache' )
                                              && !$integrations->has( 'litespeed-cache' )
                                              && !(   $integrations->has('swift-performance', 'merge_styles')
                                                   && $integrations->get('swift-performance', 'plugin') === 'pro' ) ) {
										?>
										<div class="children-wrap<?= $options->settings_areas_parseCssFiles ? '' : ' hidden' ?>">
											<?= __( 'Additional CSS domains: ', 'shortpixel-adaptive-images' ); ?>
											<input
												type="text"
												size="40"
												name="css_domains"
												data-type="string"
												value="<?= $options->settings_areas_cssDomains; ?>"
											/>
										</div>
										<p class="description"><?php
												_e( 'By default only the the CSS files served from the site domain are parsed to avoid unnecessary work on external CSS. If you serve the CSS files from another domain, please add it above. You can add multiple domains separated by commas.',
													'shortpixel-adaptive-images' );
											?></p>
										<?php
									}
									?>
									<div class="notification_popup hidden">
										<p class="text"><?= __( 'Please check your website after activating this option. If you have any other plugins that minify or otherwise handle the CSS files, this could cause conflicts and cause the background images not show.',
												'shortpixel-adaptive-images' ); ?></p>
										<input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Replace in the JS blocks', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="parse_js"
										type="checkbox"
										name="parse_js"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_parseJs, true ); ?>
									/>
									<label for="parse_js" class="tgl-btn">
										<span></span>
										<?= __( 'Parse JavaScript blocks to replace image URLs.', 'shortpixel-adaptive-images' ); ?>
									</label>
									<div class="children-wrap<?= $options->settings_areas_parseJs ? '' : ' hidden' ?>">
										<input
											id="parse_js_lazy"
											type="checkbox"
											name="parse_js_lazy"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_areas_parseJsLazy, true ); ?>
										/>
										<label for="parse_js_lazy" class="tgl-btn">
											<span></span>
											<?= __( 'Lazy-load URLs in the JS blocks.', 'shortpixel-adaptive-images' ); ?>
										</label>
									</div>
									<p class="description">
										<?= __( 'Check this if you want images which provided in JavaScript blocks to be replaced. Some galleries or plugins could place this blocks in the DOM with image URLs.',
											'shortpixel-adaptive-images' ); ?>
									</p>
									<div class="notification_popup hidden">
										<p class="text"><?= __( 'Please check your website after activating this option. Because it parses and replaces URLs inside the JavaScript blocks, it could in rare cases interfere with how the JavaScript works. Please also check if there are any errors in the browser console.',
												'shortpixel-adaptive-images' ); ?></p>
										<input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Replace in JSON data', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<input
										id="parse_json"
										type="checkbox"
										name="parse_json"
										class="tgl"
										data-type="bool"
										value="1"
										<?php checked( true, $options->settings_areas_parseJson, true ); ?>
									/>
									<label for="parse_json" class="tgl-btn">
										<span></span>
										<?= __( 'Also parse JSON AJAX calls to replace image URLs.', 'shortpixel-adaptive-images' ); ?>&nbsp;
									</label>
									<div class="children-wrap<?= $options->settings_areas_parseJson ? '' : ' hidden' ?>">
										<input
											id="parse_json_lazy"
											type="checkbox"
											name="parse_json_lazy"
											class="tgl"
											data-type="bool"
											value="1"
											<?php checked( true, $options->settings_areas_parseJsonLazy, true ); ?>
										/>
										<label for="parse_json_lazy" class="tgl-btn">
											<span></span>
											<?= __( 'Lazy-load JSON URLs.', 'shortpixel-adaptive-images' ); ?>
										</label>
									</div>
									<p class="description clearfix">
										<?= __( 'Check this if you have specific pieces of content that are delivered by Javascript in JSON-encoded packages. Some galleries like the one in Thrive Architect, or posts grids with infinite scroll need that. Please note that in some cases, CORS warnings could show up. <a href="https://help.shortpixel.com/article/465-warning-access-to-image-has-been-blocked-by-cors-policy" target="_blank">Read more</a>.',
											'shortpixel-adaptive-images' ); ?>
									</p>
									<div class="notification_popup hidden">
										<p class="text"><?= __( 'Please check your website after activating this option. In rare cases, changing the URLs inside JSON blocks might interfere with how your site\'s JavaScript works.', 'shortpixel-adaptive-images' ); ?></p>
										<input type="button" class="button blue_link" value="<?= __( 'Activate Option', 'shortpixel-adaptive-images' ); ?>" />
									</div>
								</td>
							</tr>
                            <tr>
                                <th scope="row">
                                    <?= __( 'Integrate with Lity', 'shortpixel-adaptive-images' ); ?>
                                </th>
                                <td>
                                    <input
                                            id="lity"
                                            type="checkbox"
                                            name="lity"
                                            class="tgl"
                                            data-type="bool"
                                            value="1"
                                        <?php checked( true, $options->settings_areas_lity, true ); ?>>
                                    <label for="lity" class="tgl-btn">
                                        <span></span>
                                        <?= __( 'Integrate with the Lity lightbox.', 'shortpixel-adaptive-images' ); ?>
                                    </label>
                                    <p class="description">
                                        <?= __( 'Checks for the Lity lightbox component in the pages and replaces the images with properly resized ones. Activate this if your theme uses Lity.', 'shortpixel-adaptive-images' ); ?>
                                    </p>
                                </td>
                            </tr>
						</table>
					</div>
					<div id="exclusions" class="spai_settings_tab">
						<?php
							$eager_selectors     = $options->settings_exclusions_eagerSelectors;
							$no_resize_selectors = $options->settings_exclusions_noresizeSelectors;
							$excluded_selectors  = $options->settings_exclusions_excludedSelectors;
							$excluded_paths      = $options->settings_exclusions_excludedPaths;

							$split_selectors = [
								'eager_selectors'     => $controller->splitSelectors( $eager_selectors, ',' ),
								'no_resize_selectors' => $controller->splitSelectors( $no_resize_selectors, ',' ),
								'excluded_selectors'  => $controller->splitSelectors( $excluded_selectors, ',' ),
								'excluded_paths'      => $controller->splitSelectors( $excluded_paths, PHP_EOL ),
							];

							$exclusion_limits = [
								'selectors' => 5,
								'paths'     => 10,
							];

							$excluded_selectors_qty = count( $split_selectors[ 'eager_selectors' ] ) + count( $split_selectors[ 'no_resize_selectors' ] ) + count( $split_selectors[ 'excluded_selectors' ] );
							$excluded_paths_qty     = count( $split_selectors[ 'excluded_paths' ] );
						?>
						<table class="form-table">
							<tr>
								<th scope="row"><?= __( 'Excluded selectors', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<p class="warning error-message<?= $excluded_selectors_qty <= $exclusion_limits[ 'selectors' ] ? ' hidden' : ''; ?>" data-limit="<?= $exclusion_limits[ 'selectors' ]; ?>">
										<?= str_replace( '{{QTY}}', $excluded_selectors_qty, __( 'You already have <span>{{QTY}}</span> selectors active. Please keep the number of exclusion selectors low for best performance.', 'shortpixel-adaptive-images' ) ); ?>
									</p>
									<p style="margin-bottom: 10px;">
										<?= __( 'You can also add the excludes visually, using the front-end tool.', 'shortpixel-adaptive-images' ); ?>
										<a href="https://help.shortpixel.com/article/338-how-to-use-the-image-checker-tool" target="_blank"><?= __( 'Read more', 'shortpixel-adaptive-images' ); ?></a>.
                                    </p><p style="margin-bottom: 10px;">
                                        <?= __( 'Another method, that doesn\'t involve <strong>Excluded selectors</strong>, is to add one of the following custom attributes to your IMG tags: <strong>data-spai-eager</strong>, <strong>data-spai-noresize</strong> or <strong>data-spai-excluded</strong>, depeding on which type of exclusion you need.', 'shortpixel-adaptive-images' ); ?>
									</p>
									<div><label for="eager_selectors"><?= __( 'Don\'t lazy-load:', 'shortpixel-adaptive-images' ); ?></label><br>
										<textarea
											id="eager_selectors"
											name="eager_selectors"
											rows="5"
											data-type="string"
											data-exclusion-type="selectors"
											data-setting="exclusion"
										><?= $eager_selectors; ?></textarea>
									</div>
									<div><label for="noresize_selectors"><?= __( 'Don\'t resize:', 'shortpixel-adaptive-images' ); ?></label><br>
										<textarea
											id="noresize_selectors"
											name="noresize_selectors"
											rows="5"
											data-type="string"
											data-exclusion-type="selectors"
											data-setting="exclusion"
										><?= $no_resize_selectors; ?></textarea>
									</div>
									<div><label for="excluded_selectors"><?= __( 'Leave out completely:', 'shortpixel-adaptive-images' ); ?></label><br>
										<textarea
											id="excluded_selectors"
											name="excluded_selectors"
											rows="5"
											data-type="string"
											data-exclusion-type="selectors"
											data-setting="exclusion"
										><?= $excluded_selectors; ?></textarea>
									</div>
									<p class="description">
										<?= __( 'Specify  a coma separated list of CSS selectors for images which should be left to their original width on the page, or should be kept with their original URLs. Needed for images which can for example zoom in on hover. Keep these lists as small as possible. Rules like <strong>\'img.myclass\'</strong> are preferable as they are treated server-side at page rendering.',
											'shortpixel-adaptive-images' ); ?>
									</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?= __( 'Excluded URLs', 'shortpixel-adaptive-images' ); ?></th>
								<td>
									<p class="warning error-message<?= $excluded_paths_qty <= $exclusion_limits[ 'paths' ] ? ' hidden' : ''; ?>" data-limit="<?= $exclusion_limits[ 'paths' ]; ?>">
										<?= str_replace( '{{QTY}}', $excluded_paths_qty, __( 'You already have <span>{{QTY}}</span> URL exclusions active. Please keep the number of exclusion selectors low for best performance.', 'shortpixel-adaptive-images' ) ); ?>
									</p>
									<label>
										<textarea
											id="excluded_paths"
											name="excluded_paths"
											rows="5"
											data-type="string"
											data-exclusion-type="urls"
											data-setting="exclusion"
											data-separator="<?= PHP_EOL; ?>"
										><?= $excluded_paths; ?></textarea>
									</label>
									<p class="description">
										<?= __( 'Specify a list of URL exclusion rules, one per line. An exclusion rule starts either by '
										        . '<strong>path:</strong> or by <strong>regex:</strong>. After the colon:', 'shortpixel-adaptive-images' ); ?>
									</p>
									<ul>
										<li>
											<i><?= __( 'If it\'s a <strong>regex:</strong>, you can specify a full regex (ex: /.*\.gif$/i will exclude GIF images).', 'shortpixel-adaptive-images' ); ?></i>
										</li>
										<li>
											<i><?= __( 'If it\'s a <strong>path:</strong> rule, you can specify full URLs, '
											           . 'domain names like gravatar.com or paths like /my-custom-image-folder/.', 'shortpixel-adaptive-images' ); ?></i>
										</li>
									</ul>
									<p class="description">
										<?= __( ' You can test your regex online, for example here: <a href="https://regex101.com/" target="_blank">regex101.com</a>.'
										        . ' The rule for gravatar.com is included by default because many sites use gravatar and these images cannot be optimized, '
										        . 'but if you\'re sure your site doesn\'t include gravatar URLs, feel free to remove it. ', 'shortpixel-adaptive-images' ); ?>
										<a href="https://help.shortpixel.com/article/229-how-to-exclude-images-from-optimization-in-the-shortpixel-adaptive-images-plugin" target="_blank">
											<?= __( 'Read more', 'shortpixel-adaptive-images' ); ?>
										</a>
									</p>
								</td>
							</tr>
						</table>
					</div>
					<?php submit_button( '', 'blue_link', 'submit', true, $submit_button_attributes ); ?>
				</form>
			</div>
			<div class="clear"></div>
		</div>
	</div>
