<?php
/**
 * Class: Eac_Acf_Group_Image
 *
 * @return Affiche les IMAGEs d'un champ ACF de type 'GROUP' pour l'article courant
 *
 * @since 1.8.3
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Group_Image extends Data_Tag {
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Post_Main_Id_Trait;

	public function get_name() {
		return 'eac-addon-group-image-acf-values';
	}

	public function get_title() {
		return esc_html__( 'Groupe image', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::IMAGE_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_group_image_key';
	}

	protected function register_controls() {

		$this->add_control(
			'acf_group_image_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields(), '', 'group' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Alternative', 'eac-components' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
	}

	/**
	 * get_value
	 *
	 * @param $group_key
	 * @param $sub_field_key
	 * @param $sub_meta_key
	 * @since 1.8.4
	 */
	public function get_value( array $options = array() ) {
		$field_value = '';
		$field       = array();
		$key         = $this->get_settings( 'acf_group_image_key' );
		$data_image  = array(
			'id'  => null,
			'url' => '',
		);

		if ( ! empty( $key ) ) {
			list($group_key, $sub_field_key, $sub_meta_key) = array_pad( explode( '::', $key ), 3, '' );
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $sub_field_key );

			/**
			 * @since 1.8.4
			 * Le nom du champ est = 'field_group_key_field_key'
			 * On calcule la meta_key
			 */
			$meta_key = Eac_Acf_Lib::get_acf_field_name( $sub_field_key, $sub_meta_key, $real_id );

			// Pas de meta_key pour le champ
			if ( empty( $meta_key ) ) {
				return $data_image;
			}

			if ( have_rows( $group_key ) ) {
				the_row();
				$field = get_field_object( $meta_key, $real_id );
			}
			reset_rows();

			if ( $field && ! empty( $field['value'] ) ) {
				// La valeur par défaut du champ (image)
				$field_value = $field['value'];

				switch ( $field['return_format'] ) {
					case 'array':
						$data_image = array(
							'id'  => $field_value['ID'],
							'url' => $field_value['url'],
						);
						break;
					case 'url':
						$data_image = array(
							'id'  => attachment_url_to_postid( $field_value ), // @since 1.8.7
							'url' => $field_value,
						);
						break;
					case 'id':
						$src        = wp_get_attachment_image_src( $field_value, $field['preview_size'] );
						$data_image = array(
							'id'  => $field_value,
							'url' => $src[0],
						);
						break;
				}
			}
		}

		// Valeur par défaut
		if ( empty( $field_value ) && $this->get_settings( 'fallback' ) ) {
			$field_value = $this->get_settings( 'fallback' );
			if ( ! empty( $field_value ) && is_array( $field_value ) ) {
				$data_image['id']  = $field_value['id'];
				$data_image['url'] = $field_value['url'];
			}
		}

		return $data_image;
	}

	protected function get_acf_supported_fields() {
		return array( 'image' );
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
