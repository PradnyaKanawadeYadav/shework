<?php
/**
 * Class: Eac_Acf_Group_Date
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'URL'
 *
 * @return La valeur d'un champ ACF dans un type Group 'Date - Date time' pour l'article courant
 *
 * @since 2.1.7
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Group_Date extends Data_Tag {
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Acf\DynamicTags\Tags\Traits\Post_Main_Id_Trait;

	public function get_name() {
		return 'eac-addon-group-date-acf-values';
	}

	public function get_title() {
		return esc_html__( 'Groupe date heure', 'eac-components' );
	}

	public function get_group() {
		return 'eac-acf-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::DATETIME_CATEGORY,
			TagsModule::TEXT_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'acf_date_key';
	}

	protected function register_controls() {

		$output_format_options = array(
			'default'    => esc_html__( 'Format de sortie ACF', 'eac-components' ),
			'Y-m-d H:i'  => date_i18n( 'Y-m-d H:i' ) . ' (Y-m-d H:i)',
			'F j, Y H:i' => date_i18n( 'F j, Y H:i' ) . ' (F j, Y H:i)',
			'm/d/Y H:i'  => date_i18n( 'm/d/Y H:i' ) . ' (m/d/Y H:i)',
			'd/m/Y H:i'  => date_i18n( 'd/m/Y H:i' ) . ' (d/m/Y H:i)',
			'Ymd H:i'    => date_i18n( 'Ymd H:i' ) . ' (Ymd H:i)',
			'custom'     => esc_html__( 'Personnalité', 'eac-components' ),
		);

		$this->add_control(
			'acf_date_key',
			array(
				'label'       => esc_html__( 'Champ', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields(), '', 'group' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'acf_date_fallback',
			array(
				'label'           => esc_html__( 'Alternative', 'eac-components' ),
				'type'            => Controls_Manager::DATE_TIME,
				'picker_options'  => array(
					'dateFormat'  => 'Y-m-d H:i',
					'allowInput'  => true,
					'defaultDate' => gmdate( 'Y-m-d H:i', strtotime( 'now' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				),
				'placeholder'     => 'Y-m-d H:i',
				'label_block'     => true,
				'condition'       => array( 'acf_date_key' => '' ),
			)
		);

		$this->add_control(
			'acf_date_format',
			array(
				'label'       => esc_html__( 'Format de sortie', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'description' => sprintf(
					/* translators: 1: Date format */
					esc_html__( 'Format de date %1$s', 'eac-components' ),
					'<a href="https://flatpickr.js.org/formatting/#date-formatting-tokens" target="_autre" rel="noopener noreferrer nofollow">Site</a>'
				),
				'options'     => $output_format_options,
				'default'     => 'Y-m-d H:i',
				'label_block' => true,
			)
		);

		$this->add_control(
			'acf_date_custom',
			array(
				'label'       => esc_html__( 'Format personnalisé', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'Y-m-d H:i',
				'ai'          => array( 'active' => false ),
				'label_block' => true,
				'condition'   => array(
					'acf_date_format' => 'custom',
				),
			)
		);
	}

	public function get_value( array $options = array() ) {
		$key                  = $this->get_settings( 'acf_date_key' );
		$fallback_key         = $this->get_settings( 'acf_date_fallback' );
		$output_format        = $this->get_settings( 'acf_date_format' );
		$output_format_custom = $this->get_settings( 'acf_date_custom' );
		$field                = array();
		$date_time            = false;

		if ( ! empty( $key ) ) {
			list($group_key, $sub_field_key, $sub_meta_key) = array_pad( explode( '::', $key ), 3, '' );
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $sub_field_key );

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
				$date_time = \DateTime::createFromFormat( $field['return_format'], $field['value'], new \DateTimeZone( wp_timezone_string() ) );
				if ( 'default' === $output_format ) {
					$output_format = $field['return_format'];
				}
			}
		} elseif ( $fallback_key && ! empty( $fallback_key ) ) {
			$date_time = \DateTime::createFromFormat( 'Y-m-d H:i', $fallback_key, new \DateTimeZone( wp_timezone_string() ) );
			if ( 'default' === $output_format ) {
				$output_format = 'Y-m-d H:i';
			}
		}

		if ( 'custom' === $output_format && ! empty( $output_format_custom ) ) {
			$output_format = $output_format_custom;
		}

		return $date_time instanceof \DateTime ? wp_kses_post( $date_time->format( $output_format ) ) : '';
	}

	protected function get_acf_supported_fields() {
		return array(
			'date_picker',
			'date_time_picker',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
