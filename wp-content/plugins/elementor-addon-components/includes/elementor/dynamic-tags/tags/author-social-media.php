<?php
/**
 * Class: Eac_Author_Social_network
 *
 * @return La liste formatées des URL des médias sociaux pour l'utilisateur courant
 *
 * @since 1.6.0
 * @since 2.1.0 Affecte la global $authordata
 *              Refonte de l'affichage des médias sociaux
 * @since 2.2.8 Ajout du label pour chaque réseaux
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Author_Social_Media extends Tag {

	public function get_name() {
		return 'eac-addon-author-social-network';
	}

	public function get_title() {
		return esc_html__( 'Auteur réseaux sociaux', 'eac-components' );
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key() {
		return 'author_social_network';
	}

	protected function register_controls() {
		$this->add_control(
			'author_social_network',
			array(
				'label'       => esc_html__( 'Champs', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => '',
				'options'     => Eac_Tools_Util::get_all_social_medias_name(),
			)
		);

		$this->add_control(
			'author_social_label',
			array(
				'label'     => esc_html__( 'Ajouter les labels', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Oui', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Non', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'no',
				'toggle'    => false,
			)
		);
	}

	public function render() {
		global $authordata;

		/** La variable globale n'est pas définie */
		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			if ( $post ) {
				$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		/** Global $authordata n'est pas instanciée */
		if ( ! isset( $authordata->ID ) ) {
			return;
		}

		$keys = $this->get_settings( 'author_social_network' );
		$add_label = 'yes' === $this->get_settings( 'author_social_label' ) ? true : false;
		if ( empty( $keys ) ) {
			return;
		}

		ob_start();
		foreach ( $keys as $key ) {
			$value = get_the_author_meta( $key, $authordata->ID );
			$media = Eac_Tools_Util::get_social_media_info( $key );

			if ( ! empty( $value ) && ! is_null( $media ) ) {
				$label       = $media['name'];
				$author_meta = ucfirst( get_the_author_meta( 'display_name', $authordata->ID ) );
				$name        = ucfirst( $label ) . ' ' . esc_html__( 'de', 'eac-components' ) . ' ' . $author_meta;

				if ( 'email' === $key ) {
					$email     = sanitize_email( $value );
					$email_obf = str_contains( $email, '@' ) ? explode( '@', $email )[0] . '#actus.' . explode( '@', $email )[1] : '';
					echo '<a class="eac-accessible-link obfuscated-link" href="#" data-link="' . esc_attr( $email_obf ) . '" rel="nofollow" aria-label="' . esc_attr( $name ) . '">';
				} elseif ( 'url' === $key ) {
					echo '<a class="eac-accessible-link" href="' . esc_url( $value ) . '" rel="nofollow" aria-label="' . esc_attr__( 'Voir le site web', 'eac-components' ) . '">';
				} elseif ( 'phone' === $key ) {
					$label     = $value;
					$url_phone = preg_replace( '/[^\d+]/', '', $value ?? '' );
					echo '<a class="eac-accessible-link obfuscated-tel" href="#" data-link="#actus.' . esc_attr( $url_phone ) . '" aria-label="' . esc_attr( $name ) . '">';
				} else {
					echo '<a class="eac-accessible-link" href="' . esc_url( $value ) . '" rel="nofollow" aria-label="' . esc_attr( $name ) . '">';
				}
				$class_value = simplexml_load_string( $media['icon'] );
				$icon = array(
					'library' => $media['library'],
					'value' => $class_value->attributes()->{'class'},
				);
				echo '<span class="dynamic-tags_social-icon eac-icon-svg ' . esc_html( $key ) . '">';
				Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				if ( $add_label ) {
					echo '<span dir="ltr" class="dynamic-tags_social-label ' . esc_attr( $key ) . '">' . esc_html( ucfirst( $label ) ) . '</span>';
				}
				echo '</span></a>';
			}
		}
		$output = ob_get_clean();

		if ( ! empty( $output ) ) {
			echo '<div class="dynamic-tags_social-container">';
			echo wp_kses_post( $output );
			echo '</div>';
		}
	}
}
