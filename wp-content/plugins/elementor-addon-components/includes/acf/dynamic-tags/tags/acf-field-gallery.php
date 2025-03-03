<?php
/**
 * Class: Eac_Acf_Gallery
 *
 * @return un tableau d'ID et URL des images d'un champ ACF de type 'GALLERY' pour l'article courant
 *
 * @since 2.3.0
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Post_Main_Id_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Acf_Default_Field_Trait;

	public function get_name() {
		return 'eac-addon-gallery-acf-values';
	}

	public function get_title() {
		return esc_html__( "Galerie d'images", 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::GALLERY_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_gallery_key';
	}

	protected function register_controls() {

		$this->add_control(
			'acf_gallery_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);

		$this->register_default_field_control();
	}

	/**
	 * get_value
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_value( array $options = array() ): array {
		$key           = $this->get_settings( 'acf_gallery_key' );
		$fallback_key = $this->get_settings( 'fallback_acf_field_key' );
		$data_gallery  = array();
		$field_key     = '';

		if ( ! empty( $key ) ) {
			list($field_key, $meta_key) = array_pad( explode( '::', $key ), 2, '' );
		} elseif ( ! empty( $fallback_key ) ) {
			$field_key = trim( esc_html( $fallback_key ) );
		}

		if ( ! empty( $field_key ) ) {
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $field_key );

			/**
			 * get_field_object
			 * Impérativement 3ème param à false
			 * sinon renvoie le post attachment au lieu de l'ID
			 */
			$field = get_field_object( $field_key, $real_id, false );

			if ( $field && ! empty( $field['value'] ) ) {
				foreach ( $field['value'] as $attachment_id ) {
					$data_gallery[] = array(
						'id'  => $attachment_id,
					);
				}
			}
		}
		return $data_gallery;
	}

	protected function get_acf_supported_fields() {
		return array(
			'eac_gallery',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
