<?php 
/**
 * Landing Page Render
 */

//add_action('template_include', 'seedprod_lppage_render');
add_filter( 'template_include','seedprod_pro_lppage_render');

function seedprod_pro_lppage_render($template){
  global $post;
  if(!empty($post)){
  $has_settings = get_post_meta( $post->ID, '_seedprod_page', true );
  if (!empty($has_settings) && $post->post_type = 'page') {
    
    $template = SEEDPROD_PRO_PLUGIN_PATH.'resources/views/seedprod-preview.php';
    add_action('wp_enqueue_scripts', 'seedprod_pro_deregister_styles', PHP_INT_MAX);
  } 
  }
  return $template;
}

// clean theme styles on our custom landing pages

function seedprod_pro_deregister_styles(){
  global $wp_styles;
 //var_dump($wp_styles->registered);
  foreach ($wp_styles->queue as $handle) {
      //echo '<br> '.$handle;
      if (strpos($wp_styles->registered[$handle]->src, 'wp-content/themes') !== false) {
      //var_dump($wp_styles->registered[$handle]->src);
          wp_dequeue_style($handle);        
          wp_deregister_style($handle);
      }
  }
};