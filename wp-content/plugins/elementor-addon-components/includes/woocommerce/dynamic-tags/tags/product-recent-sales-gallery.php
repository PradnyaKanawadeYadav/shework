<?php
/**
 * Class: Eac_Product_Recent_Sales_Gallery
 *
 * @return créer un tableau d'ID des images des dernières ventes
 * Ce n'est pas le total des ventes récentes par produit
 * @since 2.2.3
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Product_Recent_Sales_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags\Traits\Eac_Product_Woo_Traits;

	public function get_name() {
		return 'eac-addon-woo-recent-sales-gallery';
	}

	public function get_title() {
		return esc_html__( 'Galerie des ventes récentes', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'woo_recent_status',
			array(
				'label'       => esc_html__( 'Statut des commandes', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => array(
					'wc-pending'   => esc_html__( 'Paiement en attente', 'eac-components' ),
					'wc-completed' => esc_html__( 'Paiement effectué', 'eac-components' ),
				),
				'default'     => array( 'wc-completed' ),
				'multiple'    => true,
			)
		);

		$this->add_control(
			'woo_recent_days',
			array(
				'label'   => esc_html__( 'Nombre de jours', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 1000,
				'step'    => 7,
				'default' => 7,
			)
		);

		$this->add_control(
			'woo_recent_count',
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
		$status   = $this->get_settings( 'woo_recent_status' );
		$days     = $this->get_settings( 'woo_recent_days' );
		$count    = $this->get_settings( 'woo_recent_count' );
		$termid   = $this->get_settings( 'product_category' );
		$value    = array();

		$all_orders = wc_get_orders(
			array(
				'limit'      => -1,
				'status'     => $status,
				'date_after' => date_i18n( 'Y-m-d', strtotime( "-{$days} days" ) ),
				'return'     => 'ids',
			)
		);

		foreach ( $all_orders as $all_order ) {
			$order = wc_get_order( $all_order );
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( 'line_item' !== $item->get_type() ) {
					continue;
				}
				$product = $item->get_product();
				if ( ! is_a( $product, 'WC_Product' ) || 'simple' !== $product->get_type() ) {
					continue;
				}

				$product_id = $product->get_id();
				if ( ! empty( $termid ) && ! has_term( $termid, 'product_cat', $product_id ) ) {
					continue;
				}

				$thumb_id   = $product->get_image_id();
				if ( $thumb_id ) {
					$qty                           = isset( $value[ $product->get_slug() ] ) ? $item->get_quantity() + (int) explode( '::', implode( $value[ $product->get_slug() ] ) )[3] : $item->get_quantity();
					$value[ $product->get_slug() ] = array( 'id' => $thumb_id . '::recent::' . $product_id . '::' . $qty );
				}
			}
		}

		if ( ! empty( $value ) ) {
			ksort( $value, SORT_STRING );
			return array_slice( $value, 0, $count, true );
		}
		return $value;
	}
}
