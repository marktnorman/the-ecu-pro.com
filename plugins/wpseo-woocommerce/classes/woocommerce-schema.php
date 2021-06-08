<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Class WPSEO_WooCommerce_Schema
 */
class WPSEO_WooCommerce_Schema {

	/**
	 * The schema data we're going to output.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * WooCommerce SEO Options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * WPSEO_WooCommerce_Schema constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'wpseo_woo' );

		add_filter( 'woocommerce_structured_data_review', array( $this, 'change_reviewed_entity' ) );
		add_filter( 'woocommerce_structured_data_product', array( $this, 'change_product' ), 10, 2 );
		add_filter( 'woocommerce_structured_data_type_for_page', array( $this, 'remove_woo_breadcrumbs' ) );
		add_filter( 'wpseo_schema_webpage', array( $this, 'filter_webpage' ) );
		add_action( 'wp_footer', array( $this, 'output_schema_footer' ) );
	}

	/**
	 * Should the yoast schema output be used.
	 *
	 * @return boolean Whether or not the Yoast SEO schema should be output.
	 */
	public static function should_output_yoast_schema() {
		return apply_filters( 'wpseo_json_ld_output', true );
	}

	/**
	 * Outputs the Woo Schema blob in the footer.
	 */
	public function output_schema_footer() {
		if ( empty( $this->data ) || $this->data === array() ) {
			return;
		}

		WPSEO_Utils::schema_output( array( $this->data ), 'yoast-schema-graph yoast-schema-graph--woo yoast-schema-graph--footer' );
	}

	/**
	 * Changes the WebPage output to point to Product as the main entity.
	 *
	 * @param array $data Product Schema data.
	 *
	 * @return array $data Product Schema data.
	 */
	public function filter_webpage( $data ) {
		if ( is_product() ) {
			$data['@type'] = 'ItemPage';
		}
		if ( is_checkout() || is_checkout_pay_page() ) {
			$data['@type'] = 'CheckoutPage';
		}

		return $data;
	}

	/**
	 * Changes the Review output to point to Product as the reviewed Item.
	 *
	 * @param array $data Review Schema data.
	 *
	 * @return array $data Review Schema data.
	 */
	public function change_reviewed_entity( $data ) {
		unset( $data['@type'] );
		unset( $data['itemReviewed'] );

		$this->data['review'][] = $data;

		return array();
	}

	/**
	 * Filter Schema Product data to work.
	 *
	 * @param array       $data    Schema Product data.
	 * @param \WC_Product $product Product object.
	 *
	 * @return array $data Schema Product data.
	 */
	public function change_product( $data, $product ) {
		$canonical = $this->get_canonical();

		// Make seller refer to the Organization.
		if ( ! empty( $data['offers'] ) ) {
			foreach ( $data['offers'] as $key => $val ) {
				$data['offers'][ $key ]['seller'] = array(
					'@id' => trailingslashit( WPSEO_Utils::get_home_url() ) . WPSEO_Schema_IDs::ORGANIZATION_HASH,
				);
			}
		}

		// We're going to replace the single review here with an array of reviews taken from the other filter.
		$data['review'] = array();

		// This product is the main entity of this page, so we set it as such.
		$data['mainEntityOfPage'] = array(
			'@id' => $canonical . WPSEO_Schema_IDs::WEBPAGE_HASH,
		);

		// Now let's add this data to our overall output.
		$this->data = $data;

		$this->add_image( $canonical );
		$this->add_brand( $product );
		$this->add_manufacturer( $product );

		return array();
	}

	/**
	 * Removes the Woo Breadcrumbs from their Schema output.
	 *
	 * @param array $types Types of Schema Woo will render.
	 *
	 * @return array $types Types of Schema Woo will render.
	 */
	public function remove_woo_breadcrumbs( $types ) {
		foreach ( $types as $key => $type ) {
			if ( $type === 'breadcrumblist' ) {
				unset( $types[ $key ] );
			}
		}

		return $types;
	}

	/**
	 * Add brand to our output.
	 *
	 * @param \WC_Product $product Product object.
	 */
	private function add_brand( $product ) {
		if ( ! empty( $this->options['schema_brand'] ) ) {
			$this->add_organization_for_attribute( 'brand', $product, $this->options['schema_brand'] );
		}
	}

	/**
	 * Add manufacturer to our output.
	 *
	 * @param \WC_Product $product Product object.
	 */
	private function add_manufacturer( $product ) {
		if ( ! empty( $this->options['schema_manufacturer'] ) ) {
			$this->add_organization_for_attribute( 'manufacturer', $product, $this->options['schema_manufacturer'] );
		}
	}

	/**
	 * Adds an attribute to our Product data array with the value from a taxonomy, as an Organization,
	 *
	 * @param string      $attribute The attribute we're adding to Product.
	 * @param \WC_Product $product   The WooCommerce product we're working with.
	 * @param string      $taxonomy  The taxonomy to get the attribute's value from.
	 */
	private function add_organization_for_attribute( $attribute, $product, $taxonomy ) {
		$term = $this->get_primary_term_or_first_term( $taxonomy, $product->get_id() );

		if ( $term !== null ) {
			$this->data[ $attribute ] = array(
				'@type' => 'Organization',
				'name'  => $term->name,
			);
		}
	}

	/**
	 * Adds image schema.
	 *
	 * @param string $canonical The product canonical.
	 */
	private function add_image( $canonical ) {
		/**
		 * WooCommerce will set the image to false if none is available. This is incorrect schema and we should fix it
		 * for our users for now.
		 *
		 * See https://github.com/woocommerce/woocommerce/issues/24188.
		 */
		if ( $this->data['image'] === false ) {
			unset( $this->data['image'] );
		}

		if ( has_post_thumbnail() ) {
			$this->data['image'] = array(
				'@id' => $canonical . WPSEO_Schema_IDs::PRIMARY_IMAGE_HASH,
			);

			return;
		}

		// Fallback to WooCommerce placeholder image.
		if ( function_exists( 'wc_placeholder_img_src' ) ) {
			$image_schema        = new WPSEO_Schema_Image( $canonical . '#woocommerceimageplaceholder' );
			$this->data['image'] = $image_schema->generate_from_url( wc_placeholder_img_src() );
		}
	}

	/**
	 * Tries to get the primary term, then the first term, null if none found.
	 *
	 * @param string $taxonomy_name Taxonomy name for the term.
	 * @param int    $post_id       Post ID for the term.
	 *
	 * @return WP_Term|null The primary term, the first term or null.
	 */
	protected function get_primary_term_or_first_term( $taxonomy_name, $post_id ) {
		$primary_term    = new WPSEO_Primary_Term( $taxonomy_name, $post_id );
		$primary_term_id = $primary_term->get_primary_term();

		if ( $primary_term_id !== false ) {
			$primary_term = get_term( $primary_term_id );
			if ( $primary_term instanceof WP_Term ) {
				return $primary_term;
			}
		}

		$terms = get_the_terms( $post_id, $taxonomy_name );

		if ( is_array( $terms ) && count( $terms ) > 0 ) {
			return $terms[0];
		}

		return null;
	}

	/**
	 * Retrieves the canonical URL for the current page.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string The canonical URL.
	 */
	protected function get_canonical() {
		return WPSEO_Frontend::get_instance()->canonical( false );
	}
}
