<?php
/**
 * Class: Eac_Acf_Group_Number
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'URL'
 *
 * @return Affiche les NUMBERs d'un champ ACF de type 'GROUP' pour l'article courant
 *
 * @since 1.8.3
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Group_Number extends Tag {
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Post_Main_Id_Trait;

	public function get_name() {
		return 'eac-addon-group-number-acf-values';
	}

	public function get_title() {
		return esc_html__( 'Groupe nombre', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_group_number_key';
	}

	protected function register_controls() {

		$this->add_control(
			'acf_group_number_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields(), '', 'group' ),
				'label_block' => true,
			)
		);
	}

	/**
	 * render
	 *
	 * @param $group_key
	 * @param $sub_field_key
	 * @param $sub_meta_key
	 */
	public function render() {
		$field_value = '';
		$field       = array();
		$key         = $this->get_settings( 'acf_group_number_key' );

		if ( ! empty( $key ) ) {
			list($group_key, $sub_field_key, $sub_meta_key) = array_pad( explode( '::', $key ), 3, '' );
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $sub_field_key );

			/**
			 * Le nom du champ est = 'field_group_key_field_key'
			 * On calcule la meta_key
			 */
			$meta_key = Eac_Acf_Lib::get_acf_field_name( $sub_field_key, $sub_meta_key, $real_id );

			// Pas de meta_key pour le champ
			if ( empty( $meta_key ) ) {
				return;
			}

			if ( have_rows( $group_key ) ) {
				the_row();
				$field = get_field_object( $meta_key, $real_id );
			}
			reset_rows();

			if ( $field && ! empty( $field['value'] ) ) {
				$field_value = $field['value'];
			} else {
				$field_value = get_post_meta( $real_id, $meta_key, true );
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value ); }
			}
		}

		echo wp_kses_post( $field_value );
	}

	protected function get_acf_supported_fields() {
		return array(
			'number',
			'range',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
