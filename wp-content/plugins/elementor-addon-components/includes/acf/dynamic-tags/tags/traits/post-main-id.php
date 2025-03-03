<?php
/**
 * Calcule la valeur de l'ID réel du post/template
 *
 * @since 2.2.4 Création du trait
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\Eac_Acf_Options_Page;

trait Post_Main_Id_Trait {

	public function get_post_template_id( $field = '' ) {
		$current_id      = get_the_ID();
		$options_page_id = '';

		// Les pages d'options globales
		if ( class_exists( Eac_Acf_Options_Page::class ) && ! empty( $field ) ) {
			$options_page_id = Eac_Acf_Options_Page::get_options_page_id( $field );
		}

		if ( ! empty( $options_page_id ) ) {
			$current_id = $options_page_id;
		} elseif ( \Elementor\Plugin::$instance->documents->get_current() !== null ) { // L'ID effectif peut être un template
			$current_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		return $current_id;
	}
}
