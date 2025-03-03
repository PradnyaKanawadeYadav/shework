<?php
/**
 * Class: Eac_Product_Featured_Gallery
 *
 * @return créer un tableau d'ID des images des produits vedettes
 * @since 2.2.2
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Product_Featured_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags\Traits\Eac_Product_Woo_Traits;

	public function get_name() {
		return 'eac-addon-woo-featured-gallery';
	}

	public function get_title() {
		return esc_html__( 'Galerie des produits vedettes', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'woo_featured',
			array(
				'label'   => esc_html__( 'Nombre de produits', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
				'default' => 4,
			)
		);

		$this->register_product_term_control();
	}

	public function get_value( array $options = array() ) {
		$limit  = $this->get_settings( 'woo_featured' );
		$termid = $this->get_settings( 'product_category' );
		$value  = array();

		$products = wc_get_products(
			array(
				'post_status' => 'publish',
				'limit'       => absint( $limit ),
				'orderby'     => 'name',
				'order'       => 'ASC',
				'parent'      => 0,
				'include'     => wc_get_featured_product_ids(),
			)
		);

		if ( ! is_wp_error( $products ) && ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$product_id = $product->get_id();
				if ( ! empty( $termid ) && ! has_term( $termid, 'product_cat', $product_id ) ) {
					continue;
				}

				$thumb_id = $product->get_image_id();
				if ( $thumb_id ) {
					$value[] = array( 'id' => $thumb_id . '::featured::' . $product_id );
				}
			}
		}
		return $value;
	}
}
