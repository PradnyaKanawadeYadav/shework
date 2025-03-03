<?php
/**
 * Class: Eac_Acf_Group_Gallery
 *
 * @return un tableau d'ID et URL des images d'un champ ACF de type GROUP 'GALLERY' pour l'article courant
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

class Eac_Acf_Group_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Post_Main_Id_Trait;

	public function get_name() {
		return 'eac-addon-group-gallery-acf-values';
	}

	public function get_title() {
		return esc_html__( "Groupe galerie d'images", 'eac-components' );
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
		return 'acf_group_gallery_key';
	}

	protected function register_controls() {
		$this->add_control(
			'acf_group_gallery_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields(), '', 'group' ),
				'label_block' => true,
			)
		);
	}

	/**
	 * get_value
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_value( array $options = array() ): array {
		$key          = $this->get_settings( 'acf_group_gallery_key' );
		$data_gallery = array();

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
				return $data_gallery;
			}

			/**
			 * get_field_object
			 * Impérativement 3ème param à false
			 * sinon renvoie le post attachment au lieu de l'ID
			 */
			if ( have_rows( $group_key ) ) {
				the_row();
				$field = get_field_object( $meta_key, $real_id, false );
			}
			reset_rows();

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
