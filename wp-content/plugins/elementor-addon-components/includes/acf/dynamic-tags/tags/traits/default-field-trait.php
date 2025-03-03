<?php
/**
 * Les contrôles des champs ACF par défaut
 *
 * @since 2.3.2
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

trait Acf_Default_Field_Trait {

	protected function register_default_field_control() {

		$this->add_control(
			'fallback_acf_field_key',
			array(
				'label'       => esc_html__( 'Alternative: Clé du champ', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'field_xxxxxxx',
				'dynamic'     => array( 'active' => false ),
				'ai'          => array( 'active' => false ),
				'label_block' => true,
				'separator'   => 'before',
			)
		);
	}
}
