<?php
/**
 * Class: Url_All_Tag
 *
 * @return affiche la liste de toutes les URL
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Eac_All_Url_Tag extends Data_Tag {

	public function get_name() {
		return 'eac-addon-all-url-tag';
	}

	public function get_title() {
		return esc_html__( 'Toutes les URL', 'eac-components' );
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		//return 'single_all_url'; // Provoque une erreur avec l'attribut 'groups' du 'select'
	}

	protected function register_controls() {
		$this->add_control(
			'single_all_url',
			array(
				'label'       => esc_html__( 'URL', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => $this->get_all_posts_url(),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'single_all_url' );
		if ( empty( $param_name ) ) { return ''; }
		return esc_url( $param_name );
	}

	private function get_all_posts_url() {
		$groups = array();
		$post_types = Eac_Tools_Util::get_filter_post_types();

		foreach ( $post_types as $post_type_name => $post_type ) {
			$all_posts           = array();
			$options             = array();
			list($name , $label) = explode( '::', $post_type );

			$all_posts = $this->get_all_posts_data( $name );
			if ( ! empty( $all_posts ) && ! is_wp_error( $all_posts ) ) {
				foreach ( $all_posts as $simple_post ) {
					$options[ esc_url( get_permalink( $simple_post->ID ) ) ] = esc_html( $simple_post->post_title );
				}
				if ( empty( $options ) ) {
					continue;
				}
				asort( $options, SORT_STRING );
				$groups[] = array(
					'label'   => esc_html( $label ),
					'options' => $options,
				);
			}
		}
		return $groups;
	}

	/**
	 * get_all_posts_data
	 *
	 * Retourne la liste des données des articles personnalisés
	 *
	 * @return ID, post_title
	 */
	private function get_all_posts_data( $post_type ) {
		global $wpdb;

		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title
				FROM {$wpdb->prefix}posts
				WHERE post_type = %s
				AND post_title != ''
				AND post_status = 'publish'",
				$post_type
			)
		);

		return $result;
	}
}
