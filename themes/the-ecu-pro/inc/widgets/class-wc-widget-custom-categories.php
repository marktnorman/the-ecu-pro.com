<?php
/**
 * Product Custom Categories Widget
 *
 * @package WooCommerce/Widgets
 * @version 2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product custom categories widget class.
 *
 * @extends WC_Widget
 */
class WC_Widget_Product_Custom_Categories extends WC_Widget {

	/**
	 * Category ancestors.
	 *
	 * @var array
	 */
	public $cat_ancestors;

	/**
	 * Current Category.
	 *
	 * @var bool
	 */
	public $current_cat;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_product_categories widget_product_custom_categories';
		$this->widget_description = __( 'A list of product categories.', 'woocommerce' );
		$this->widget_id          = 'woocommerce_product_custom_categories';
		$this->widget_name        = __( 'Custom Product Categories', 'woocommerce' );
		$this->settings           = array(
			'title'              => array(
				'type'  => 'text',
				'std'   => __( 'Product categories', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $wp_query, $post, $wp;
		$seachArgs = explode("?", add_query_arg( $wp->query_vars, home_url()))[1];
		$productCats = str_replace("product_cat=", "", (str_replace("s&post_type=product&","",$seachArgs)));
		if (explode("/", $productCats)){
			$productCats = explode("/", $productCats);
			$link = '';
			$category_object = '';
			echo '<ul class="custom-cat product-categories">';
				// var_dump ($productCats);
				foreach($productCats as $cat){
					$category_object = get_term_by('slug', $cat, 'product_cat');
					$link = get_category_link($category_object->term_id);
					// var_dump($category_object);
					if(strpos($cat,"=") || $cat == "0")
						$catName = "All Categories";
					else
						$catName = $cat;
					echo '<li class="cat-item"><i class="fa fa-angle-left" aria-hidden="true"></i><a href="'. $link . '">' . $catName . '</a></li>';
				}
				$args = array(
					'hide_empty'  => true,
					'parent'      => $category_object->term_id,
					'hierarchical' => false,
					'taxonomy'    =>'product_cat'
				);

				$child_cats = get_categories($args);
				// var_dump($child_cats);
				echo '<ul class="child-cat-item">';
				foreach ($child_cats as $value) {
					// var_dump($value);
					echo '<li class="item"><a href="' . get_category_link($value->term_id) . '">'. $value->name. '</a></li>';
				}
				echo '</ul>';

			echo '</ul>';
		}
		$this->widget_end( $args );
	}
}