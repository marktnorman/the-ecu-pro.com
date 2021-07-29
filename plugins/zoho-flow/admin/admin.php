<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
add_action( 'admin_init', 'zoho_flow_admin_init', 10, 0 );

function zoho_flow_admin_init() {
	do_action( 'wp_zoho_flow_admin_init' );
}

add_action( 'admin_menu', 'zoho_flow_admin_menu', 9, 0 );
function zoho_flow_admin_menu() {
	global $_wp_last_object_menu;

	$_wp_last_object_menu++;

	do_action( 'zoho_flow_admin_menu' );

	add_menu_page( 
		__( 'Zoho Flow', 'zoho-flow' )
		,__( 'Zoho Flow', 'zoho-flow' )
		,'zoho_flow_admin_page'
		, 'zoho_flow'
		,'zoho_flow_show_admin_page'
		, 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAxMDI0IDEwMjQiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDEwMjQgMTAyNDsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsOiMwMDA7fQoJLnN0MXtmaWxsOiMwMDk4NDk7fQoJLnN0MntmaWxsOiNGRkZGRkY7fQo8L3N0eWxlPgo8cmVjdCBjbGFzcz0ic3QwIiB3aWR0aD0iMTAyNCIgaGVpZ2h0PSIxMDI0IiB0cmFuc2Zvcm09InNjYWxlKC4wMDA2KSIvPgo8Zz4KCTxnPgoJCTxwYXRoIGNsYXNzPSJzdDIiIGQ9Ik0yMTEuMDMsODU1LjQ4Yy0xMC4zNywwLTIwLjQ1LTUuMzgtMjYuMDEtMTVMNC4wMiw1MjdjLTUuMzYtOS4yOC01LjM2LTIwLjcyLDAtMzBsMjQxLTQxNy4zOAoJCQljNS4zNi05LjI4LDE1LjI2LTE1LDI1Ljk4LTE1aDQ4MmMxNC4xNywwLDI2LjUzLDkuODYsMjkuNDcsMjMuNzJjMi45MywxMy44NC00LjA4LDI3Ljc5LTE2Ljk5LDMzLjU2CgkJCWMtMS43OSwwLjg1LTQyLjkzLDIwLjgxLTg5LjIyLDcwLjkyYy0yNy42LDI5Ljg3LTUxLjY0LDY0LjUxLTcxLjQ3LDEwMi45NWMtMTkuODEsMzguNDEtMzUuNDUsODAuNzYtNDYuNjYsMTI2LjI5bDgyLjc2LTAuMDYKCQkJYzAuMDEsMCwwLjAxLDAsMC4wMiwwYzE2LjU2LDAsMjkuOTksMTMuNDIsMzAsMjkuOThjMC4wMSwxNi41Ny0xMy40MSwzMC4wMS0yOS45OCwzMC4wMmwtMTIwLDAuMDhjLTAuMDEsMC0wLjAxLDAtMC4wMiwwCgkJCWMtOC45MywwLTE3LjQtMy45OC0yMy4xLTEwLjg2Yy01LjctNi44OC04LjA0LTE1Ljk1LTYuMzgtMjQuNzNjMTIuNDctNjUuNzgsMzMuMDYtMTI2LjQ5LDYxLjE5LTE4MC40NgoJCQljMjIuNjgtNDMuNTEsNTAuMjktODIuNzQsODIuMDUtMTE2LjYxYzguNTYtOS4xMiwxNi45OC0xNy4zOCwyNS4wOS0yNC44MkgyODguMzJMNjQuNjQsNTEybDE3Mi4zNCwyOTguNDcKCQkJYzguMjgsMTQuMzUsMy4zNywzMi43LTEwLjk4LDQwLjk4QzIyMS4yOCw4NTQuMTgsMjE2LjEyLDg1NS40OCwyMTEuMDMsODU1LjQ4eiIvPgoJPC9nPgoJPGc+CgkJPHBhdGggY2xhc3M9InN0MiIgZD0iTTc1Myw5NTkuMzhIMjcxYy0xNC4xNywwLTI2LjUzLTkuODYtMjkuNDctMjMuNzJjLTIuOTMtMTMuODQsNC4wOC0yNy43OSwxNi45OS0zMy41NgoJCQljMS43MS0wLjgxLDQyLjg4LTIwLjc2LDg5LjIyLTcwLjkxYzI3LjU5LTI5Ljg3LDUxLjYzLTY0LjUsNzEuNDYtMTAyLjkzYzE5LjgxLTM4LjQsMzUuNDUtODAuNzUsNDYuNjctMTI2LjI3bC04Mi43NiwwCgkJCWMtMTYuNTcsMC0zMC0xMy40My0zMC0zMGMwLTE2LjU3LDEzLjQzLTMwLDMwLTMwbDExOS45OCwwYzguOTQsMCwxNy40MSwzLjk4LDIzLjExLDEwLjg3YzUuNyw2Ljg4LDguMDMsMTUuOTUsNi4zNywyNC43MwoJCQljLTEyLjQ3LDY1Ljc2LTMzLjA2LDEyNi40Ni02MS4yLDE4MC40MWMtMjIuNjgsNDMuNS01MC4yOSw4Mi43Mi04Mi4wNSwxMTYuNThjLTguNTUsOS4xMi0xNi45NiwxNy4zNy0yNS4wNywyNC44aDM3MS40NAoJCQlMOTU5LjM2LDUxMkw3ODcuMTcsMjEzLjc4Yy04LjI4LTE0LjM1LTMuMzctMzIuNywxMC45OC00MC45OGMxNC4zNS04LjI5LDMyLjctMy4zNyw0MC45OCwxMC45OEwxMDE5Ljk4LDQ5NwoJCQljNS4zNiw5LjI4LDUuMzYsMjAuNzIsMCwzMGwtMjQxLDQxNy4zOEM3NzMuNjIsOTUzLjY3LDc2My43Miw5NTkuMzgsNzUzLDk1OS4zOHoiLz4KCTwvZz4KPC9nPgo8L3N2Zz4K'		
		,$_wp_last_object_menu );
}

add_filter( 'admin_footer_text', 'admin_footer_text', 1 );
function admin_footer_text( $footer_text ) {
	if ( ! current_user_can( 'zoho_flow_admin_page' ) ) {
		return $footer_text;
	}
	$current_screen = get_current_screen();

	if(isset( $current_screen->id ) && $current_screen->id == 'toplevel_page_zoho_flow'){
		if ( ! get_option( 'zoho_flow_admin_footer_text_rated' ) ) {
			$footer_text = sprintf(
				/* translators: 1: Zoho Flow 2:: five stars */
				esc_html__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'zoho-flow' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'Zoho Flow', 'zoho-flow' ) ),
				'<a href="https://wordpress.org/support/plugin/zoho-flow/reviews?rate=5#new-post" target="_blank" class="zoho-flow-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'zoho-flow' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}	
		else{
			$footer_text = esc_html__( 'Thank you for using Zoho Flow.', 'zoho-flow' );
		}
	}
	return $footer_text;	
}


add_action( 'wp_ajax_zoho_flow_rated', 'zoho_flow_rated' );
function zoho_flow_rated(){
	if ( ! current_user_can( 'zoho_flow_admin_page' ) ) {
		wp_die( -1 );
	}
	update_option( 'zoho_flow_admin_footer_text_rated', 1 );
	wp_die();
}



function zoho_flow_enqueue_scripts(){

	wp_register_script('zoho-flow-admin', plugins_url('../assets/js/zoho-flow-admin.js', __FILE__), array('jquery'),null, false);
	$i18n_array = array(
		'enter_description' => __('Enter description'),
		'generating' => __('Generating...'),
		'generate' => __('Generate'),
		'remove_api_key_confirmation' => __('Are you sure you want to remove the API key?'),
		'unable_to_copy_api_key' => __('Unable to copy the API key')
	);
	wp_localize_script( 'zoho-flow-admin', 'i18n', $i18n_array );
	wp_enqueue_script('zoho-flow-admin');

	wp_register_style('zoho-flow-admin', plugins_url('../assets/css/zoho-flow-admin.css', __FILE__));
	wp_enqueue_style('zoho-flow-admin');

	add_thickbox();
}

function zoho_flow_generate_api_key(){
    if ( ! current_user_can( 'zoho_flow_admin_page' ) ) {
        return new WP_Error( 'ajax_forbidden', esc_html__( 'You are not allowed to perform the operation.', 'zoho-flow'), array( 'status' => 403 ) );
    }	
	if(isset($_POST['service_id'])) {
	  if(!wp_verify_nonce($_POST['api_key_generation_nonce'],'generate_api_key')){
	      wp_send_json_error(__('Unable to generate API Key. Please try after refreshing the page.', 'zoho-flow'), 403);
	   }else{
	      $service_id = sanitize_key($_POST['service_id']);
	      $description = sanitize_text_field($_POST['description']);
	      $service = Zoho_Flow_Services::get_instance()->get_service($service_id)['instance'];
	      if(isset($service)){
	      	$result = $service->generate_api_key($description);
	      	if(is_wp_error($result)){
				if(isset($result->get_error_data()['status'])){
					$status = $result->get_error_data()['status'];
	            	wp_send_json_error($result->get_error_message(), $status);
				}
				else{
					wp_send_json_error($result, 500);
				}	      		
	      	}
	      	else{
	      		echo $result;	
	      	}
	      }
	      else{
			wp_send_json_error(__('Service not found', 'zoho-flow'), 400);
	      }
	  }
	}
	wp_die();
}
add_action( 'wp_ajax_zoho_flow_generate_api_key', 'zoho_flow_generate_api_key' );

function zoho_flow_remove_api_key(){
    if ( ! current_user_can( 'zoho_flow_admin_page' ) ) {
        return new WP_Error( 'ajax_forbidden', esc_html__( 'You are not allowed to perform the operation.', 'zoho-flow'), array( 'status' => 403 ) );
    }		
	if(isset($_POST['service_id'])) {
	  if(!wp_verify_nonce($_POST['api_key_removal_nonce'],'remove_api_key')){
	      wp_send_json_error(__('Unable to remove API Key. Please try after refreshing the page.', 'zoho-flow'), 403);
	   }else{
	   	$api_key_id = sanitize_key($_POST['api_key_id']);
	   	if(!ctype_digit($api_key_id)){
	   		wp_send_json_error(__('Invalid API Key ID provided', 'zoho-flow'), 400);
	   		wp_die();
	   		return;
	   	}
		$service_id = sanitize_key($_POST['service_id']);
		$service = Zoho_Flow_Services::get_instance()->get_service($service_id)['instance'];
		if(isset($service)){
			$result = $service->remove_api_key($api_key_id);
	        if(is_wp_error($result)){
				if(isset($result->get_error_data()['status'])){
					$status = $result->get_error_data()['status'];
	            	wp_send_json_error($result->get_error_message(), $status);
				}
				else{
					wp_send_json_error($result, 500);
				}
	        }
	        else{
				echo __('Removed API key successfully', 'zoho-flow');	
	        }
		}
		else{
			wp_send_json_error(__('Service not found', 'zoho-flow'), 400);
		}
	  }
	}
	wp_die();
}
add_action( 'wp_ajax_zoho_flow_remove_api_key', 'zoho_flow_remove_api_key' );


function zoho_flow_test_tls(){
	$resp = wp_remote_get("https://tlstest.zoho.com/api");
	if(is_wp_error($resp)){
		return $resp;
	}
	$json = json_decode($resp['body'], true);
	return $json;
}

function zoho_flow_show_admin_page(){
	zoho_flow_enqueue_scripts();
?>
<div class="wrap">
	<div class="zoho-flow-header">
		<h1 class="wp-heading-inline">
			<img class="zflow-logo" src="<?php echo plugins_url('../assets/images/flow-256.png', __FILE__); ?>"/>
			<?php echo esc_html__('Zoho Flow', 'zoho-flow') ?>
		</h1>
		<h3><?php echo esc_html__('Integrate your favourite WordPress plugins with hundreds of business apps', 'zoho-flow') ?></h3>
	</div>

<?php
	if ( !get_option('permalink_structure')){
?>
<div class="notice inline notice-warning">
	<h2><?php echo esc_html__( 'Permalink is not enabled' ) ?></h2>
    <p><?php echo esc_html__( 'It appears that permalink has not been enabled for this site. For this plugin to work properly, you need to enable permalinks. ', 'zoho-flow' )?><a href="<?php echo admin_url('options-permalink.php'); ?>">Enable</a></p>
</div>
<?php
	}
	$resp = zoho_flow_test_tls();
	if(is_wp_error($resp)){
?>
<div class="notice inline notice-warning">
	<h2><?php echo esc_html__( 'External url call failed' ) ?></h2>
	<p><?php echo esc_html__( 'This plugin needs to call external url for its functionality. However, it did not work due to the below error.', 'zoho-flow' ) ?></p>
    <p style="font-weight:bold;"><?php printf(esc_html( '%s' ), $resp->get_error_message())?></p>
</div>
<?php		
	}
	else{
		$message = $resp['message'];
		$version = $resp['version'];
		if($message != 'Connection Success'){
?>
<div class="notice inline notice-warning">
	<h2><?php echo esc_html__( 'TLS connection failed' ) ?></h2>

    <p>
    	<?php 
    		// translators: %s refers to the minimum TLS version
    		printf(esc_html__( 'This plugins requires at least %s to work properly. Kindly upgrade the TLS version of your wordpress setup.', 'zoho-flow' ), $version)
    	?>
    </p>
</div>
<?php		
		}		
	}
	if ( ! empty( $_REQUEST['service'] ) ) {
		$service_param = sanitize_key($_REQUEST['service']);
		$zoho_flow_services = Zoho_Flow_Services::get_instance();
		$service = $zoho_flow_services->get_service($service_param);
		if(is_null($service)){
			zoho_flow_show_unavailable_service();
		}
		else{
			zoho_flow_show_service_details($service);
		}
		return;
	}
	else{
		zoho_flow_show_service_list();
	}
?>
</div>
<?php

}

function zoho_flow_show_service_list(){
?>
<div class="plugin-gallery-wrapper">
	<div class="plugin-gallery">		
		<h2><?php echo esc_html__( 'Supported Plugins', 'zoho-flow' )?></h2>
		<p><?php echo esc_html__( 'Automate workflows between these WordPress plugins and other apps you use', 'zoho-flow' )?></p>
		<div class="table services-list-table">
<?php
	$servicesTable = new Zoho_Flow_Services_List_Table();
	$servicesTable->prepare_items(); 
	$servicesTable->display();
?>
		</div>
	</div>
	<div class="plugin-gallery-right-side">
		<div class="why-flow-section">
			<h2><img class="zflow-logo" src="<?php echo plugins_url('../assets/images/flow-256.png', __FILE__); ?>"><?php echo esc_html__('About Zoho Flow', 'zoho-flow') ?></h2>
			<p><?php echo esc_html__('Zoho Flow is an integration platform that helps you connect popular WordPress plugins as well as hundreds of apps using its low code builder. Simply set a trigger, add actions, use delays, decisions or other logic elements to automate complex business workflows within minutes.') ?></p>
			<p class="zflow-cta">
				<a target="_blank" href="//www.zoho.com/flow/features.html?utm_source=wordpress&utm_medium=link&utm_campaign=zoho-flow-plugin"><?php echo esc_html__('Features', 'zoho-flow') ?></a> 
				<a target="_blank" href="//www.zoho.com/flow/apps/?utm_source=wordpress&utm_medium=link&utm_campaign=zoho-flow-plugin"><?php echo esc_html__('App gallery', 'zoho-flow') ?></a> 
				<a target="_blank" href="//www.zoho.com/flow/help/?utm_source=wordpress&utm_medium=link&utm_campaign=zoho-flow-plugin"><?php echo esc_html__('Help', 'zoho-flow') ?></a>
				<a target="_blank" href="//www.zoho.com/flow/pricing.html?utm_source=wordpress&utm_medium=link&utm_campaign=zoho-flow-plugin"><?php echo esc_html__('Pricing', 'zoho-flow') ?></a>
			</p>
		</div>
		<div class="flow-video-section">
			<h2><?php echo esc_html__('Watch Zoho Flow in action') ?></h2>
			<div class="zvideo-thumb-wrap zcpopup-controller">
				<a id="open-video-popup"  href="https://www.youtube.com/embed/68JFXlpm6iI?autoplay=1&TB_iframe=true&height=400&width=600" class="thickbox">
				<img src="<?php echo plugins_url('../assets/images/flow-video-thumb.png', __FILE__); ?>">
				<span class="zarrow"></span>
				</a>
			</div>
		</div>			
		<div class="zflow-req-app">
			<div class="zicon zicon1"></div>
			<h2><?php echo esc_html__('Didn’t find an app or plugin?', 'zoho-flow') ?></h2>
			<p><?php echo esc_html__('Are we missing any important apps or plugins you use? Let us know and we’ll try our best to add them.', 'zoho-flow') ?></p>
			<a href="https://creator.zohopublic.com/zohointranet/zoho-flow/form-embed/Request_an_App/7fBw7xgDYWV0bJrNa8S0m8AVXWUFC4u42mapek0d3ySeYHNVxZK4x0JMTD8mC8Weg18tNBjKvWsT2e0vQUXC3OGWpENy7Vb4sMtN?zc_BdrClr=ffffff&TB_iframe=true&height=550&width=350" class="thickbox"><?php echo esc_html__('Request app / plugin', 'zoho-flow') ?></a>
		</div>	
	</div>
</div>
<?php	
}

function zoho_flow_show_unavailable_service(){
?>
	<div class="unavailable-zoho-flow-service">
		<img src="<?php echo plugins_url('../assets/images/no-result-found.svg', __FILE__); ?>"/>
		<h2><?php echo esc_html__( 'The plugin was not found!', 'zoho-flow' )?></h2>
	</div>
<?php
}

function zoho_flow_api_key_html($current_service){
	$service_id = $current_service->get_service_id();
	$button_label = esc_html__( 'Generate', 'zoho-flow' );
	$ok_button_label = esc_html__( 'Ok', 'zoho-flow' );
?>
<div id="api_key_details" style="display:none;">
	<form id="generate-api-key-form" method="post" style="padding:20px 0">
		<?php wp_nonce_field( 'generate_api_key', 'api_key_generation_nonce' );?>
		<input type="hidden" name="action" value="zoho_flow_generate_api_key">
		<input type="hidden" name="service_id" value="<?php echo esc_attr($service_id) ?>">
		<label for="description"><?php echo esc_html__('Provide a short description for the API key') ?></label>
		<input id="api-key-description" type="text" maxlength="100" name="description" style="display: block;
		    margin: 5px 0;
		    height: 40px;
		    width: 95%;"/>
		<span>
			<input type="button" id="generate-api-key" class="button button-primary" value="<?php echo $button_label ?>"></button>
			<span style="display:none;"><?php echo esc_html__('Generating API key...', 'zoho-flow') ?></span>
		</span>
	</form>
	<div id="api-key-div" style="display:none;">
		<p><?php echo esc_html__('Copy the API key and keep it safe. The API key cannot be retrieved again.', 'zoho-flow') ?></p>
		<span id="api-key-span" style="padding:10px;background:#f0f0f0;font-size:.9em;"></span>
		<a id="copy-api-key" class="dashicons dashicons-admin-page"></a>
		<p><button id="ok-api-key-popup" class="button button-primary"><?php echo $ok_button_label ?></button></p>
	</div>
</div>
<?php
}

function zoho_flow_show_service_details($service){
	$services = Zoho_Flow_Services::get_instance()->get_services();
	$current_service = Zoho_Flow_Services::get_instance()->get_service($service['id'])['instance'];
	$service_name = $current_service->get_service_name();
	$gallery_app_link = $service['gallery_app_link'];
	$service_name
?>
	<div class="service-details-wrapper">
		<div class="service-details-content-wrapper">

			<span>
				<a style="text-decoration:none;" href="<?php echo menu_page_url( 'zoho_flow', false ); ?>">&larr; <?php echo esc_html__( 'Back', 'zoho-flow' )?></a>
			</span>
			<div class="service-details-title">
				<div>
					<h2>
						<img src="<?php echo esc_attr(esc_url(plugins_url('/../assets/images/logos/' . $service['icon_file'], __FILE__)))?>"/><?php echo $service['name']?>
					</h2>

				</div>
			</div>		
			<div class="service_details_content">
				<div>
					<h3 style="display:inline;vertical-align:middle;"><?php echo esc_html__( 'API Keys', 'zoho-flow' )?></h3>
					<a style="vertical-align:middle;margin-left:5px;" id="open-api-key-generation-popup" href="<?php echo esc_attr('#TB_inline?width=600&height=150&inlineId=api_key_details') ?>" class="button button-primary thickbox" title="<?php echo esc_attr($service_name) ?> - <?php echo esc_attr__('Generate new API key', 'zoho-flow') ?>"><?php echo esc_html__('+ New API Key', 'zoho-flow') ?></a>
					<span id="notice" class="notice" style="display:none;margin-left:10px;padding:5px;vertical-align:middle;"></span>
					<p>
						<?php 
							// translators: %s refers to the plugin name
							printf(esc_html__( 'You can use any of the active API keys to allow Zoho Flow integrate %s with other apps. Generate one if you don\'t have any.', 'zoho-flow' ), $service_name) 
						?>
					</p>

					<form id="remove-api-key-form" method="post">
						<?php wp_nonce_field( 'remove_api_key', 'api_key_removal_nonce' );?>
						<input type="hidden" name="action" value="zoho_flow_remove_api_key">
						<input type="hidden" name="service_id" value="<?php echo esc_attr($current_service->get_service_id()) ?>">					
						<div class="table api-key-table">						
					<?php
						$api_keys_table = new Zoho_Flow_API_Key_List_Table();
						$api_keys_table->set_service_id($current_service->get_service_id());
						$api_keys_table->prepare_items(); 
						$api_keys_table->display();					
					?>
						</div>
					</form>
					<?php zoho_flow_api_key_html($current_service) ?>
				</div>
			</div>
		</div>
		<div class="service-details-right-pane">
			<h3 style="width:100%;">
				<?php 
					// translators: %s refers to the plugin name
					printf(esc_html__('How to integrate %s with other apps via Zoho Flow?', 'zoho-flow') , $service_name) 
				?>
			</h3>
			<h4><?php echo esc_html__('Step 1', 'zoho-flow') ?></h4>
			<p><a href="https://www.zoho.com/flow/signup.html?utm_source=wordpress&utm_medium=link&utm_campaign=zoho_flow_<?php echo $gallery_app_link ?>" target="_blank"><?php echo esc_html__('Register', 'zoho-flow') ?></a><?php echo esc_html__(' for an account in Zoho Flow or login if you already have one.', 'zoho-flow') ?></p>
			<h4><?php echo esc_html__('Step 2', 'zoho-flow') ?></h4>
			<p><a href="#TB_inline?width=600&height=150&inlineId=api_key_details" class="thickbox" title="<?php echo esc_attr($service_name) ?> - <?php echo esc_attr__('Generate new API key', 'zoho-flow') ?>"><?php echo esc_html__('Generate', 'zoho-flow') ?></a><?php echo esc_html__(' an API Key for the plugin. Keep it safe, as it cannot be retrieved again.', 'zoho-flow') ?></p>
			<h4><?php echo esc_html__('Step 3', 'zoho-flow') ?></h4>
			<p><?php echo esc_html__('Go to Zoho Flow and create a new flow. Each plugin supports certain triggers/actions. Pick the one you want to use in the automation.', 'zoho-flow') ?></p>			
			<h4><?php echo esc_html__('Step 4', 'zoho-flow') ?></h4>
			<p>
				<?php 
					// translators: %s refers to the plugin name
					printf( esc_html__('When you create a connection for %s, use the generated API key.', 'zoho-flow' ), $service_name ) 
				?>
			</p>
			<h4><?php echo esc_html__('Step 5', 'zoho-flow') ?></h4>
			<p><?php echo esc_html__('Add the base URL which is the part of the URL as instructed in Flow.', 'zoho-flow') ?></p>
			<h4><?php echo esc_html__('Step 6', 'zoho-flow') ?></h4>
			<p><?php echo esc_html__('Configure the trigger/action you wish to add.', 'zoho-flow') ?></p>			
			<p>
				<?php
					echo __('Note: Zoho Flow supports certain triggers/actions which you can use for your use case. Write to <a href="mailto:support@zohoflow.com">support@zohoflow.com</a> if you want to see any different trigger/action in a plugin.', 'zoho-flow' )
				?>
			</p>
			<h3 style="width:100%;"><?php printf(esc_html__('More information')) ?></h3>
			<p>
			<a target="_blank" href="https://www.zoho.com/flow/apps/<?php echo $gallery_app_link ?>/integrations/?utm_source=wordpress&utm_medium=link&utm_campaign=zoho_flow_<?php echo $gallery_app_link ?>">
				<?php 
					// translators; %s refers to the plugin name
					printf(esc_html__('%s integrations', 'zoho-flow') , $service_name) 
				?>
			</a></p>
			<p><a target="_blank" href="https://www.zoho.com/flow/help/?utm_source=wordpress&utm_medium=link&utm_campaign=zoho_flow_<?php echo $gallery_app_link ?>">Help resources</a>
			<h3 style="width:100%;"><?php printf(esc_html__('Support')) ?></h3>
			<p><a href="mailto:support@zohoflow.com">support@zohoflow.com</a></p>
		</div>
	</div>
<?php
	

}