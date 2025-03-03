<?php
/**
 * Class: EAC_Plugin
 *
 * Description:  Active l'administration du plugin avec les droits d'Admin
 * Charge la configuration, les widgets et les fonctionnalités
 *
 * @since 1.0.0
 */

namespace EACCustomWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;

/**
 * Main Plugin Class
 */
class EAC_Plugin {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * @var suffix_css
	 *
	 * Debug des fichiers CSS
	 */
	private $suffix_css = EAC_STYLE_DEBUG ? '.css' : '.min.css';

	/**
	 * @var suffix_js
	 *
	 * Debug des fichiers JS
	 */
	private $suffix_js = EAC_SCRIPT_DEBUG ? '.js' : '.min.js';

	/**
	 * Constructeur
	 */
	private function __construct() {
		$this->includes();
	}

	/**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Singletons should not be cloneable.
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html( 'Il y a quelque chose de pourri au Royaume du Danemark' ), '1.0.0' );
	}

	/**
	 * Singletons should not be restorable from strings.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, esc_html( 'Il y a quelque chose de pourri au Royaume du Danemark' ), '1.0.0' );
	}

	/**
	 * includes
	 *
	 * @return void
	 */
	private function includes(): void {
		global $wp_version;

		/** Filtre pour ajouter le type 'module' ES6 à certains scripts */
		add_filter( 'script_loader_tag', array( $this, 'add_script_module_attribute' ), 10, 3 );
		add_filter( 'style_loader_tag', array( $this, 'add_style_attribute' ), 10, 2 );

		/** Charge la configuration du plugin et des composants */
		require_once EAC_PLUGIN_PATH . 'core/eac-load-config.php';

		/** Ajoute une nouvelle capability 'eac_manage_options' aux rôles "editor' et 'shop_manager' */
		if ( current_user_can( 'manage_options' ) ) {
			$this->set_grant_option_page();
		}

		/** Charge la page d'administration du plugin */
		if ( current_user_can( 'manage_options' ) || current_user_can( Eac_Config_Elements::get_manage_options_name() ) ) {
			require_once EAC_PLUGIN_PATH . 'admin/settings/eac-load-components.php';
		}

		/** Charge les fonctionnalités */
		require_once EAC_PLUGIN_PATH . 'core/eac-load-features.php';

		/** Charge les catégories, les controls et les composants Elementor */
		require_once EAC_PLUGIN_PATH . 'core/eac-load-elements.php';

		/**
		 * Charge les scripts et les styles globaux
		 * Ajoute des colonnes et leurs contenus aux vues Elementor/Templates
		 */
		require_once EAC_PLUGIN_PATH . 'core/eac-load-scripts.php';

		/** Compatibilité du plugin avec la fonctionnalité HPOS de Woocommerce */
		add_action(
			'before_woocommerce_init',
			function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'elementor-addon-components/elementor-addon-components.php', true );
				}
			}
		);
	}

	/**
	 * set_grant_option_page
	 *
	 * Ajoute une nouvelle capability 'eac_manage_options' aux rôles "editor' et 'shop_manager'
	 *
	 * 'wp_user_roles' de la table options
	 */
	private function set_grant_option_page(): void {
		/** Options ACF Options Page && Grant Options Page sont actives */
		$grant_option_page = Eac_Config_Elements::is_feature_active( 'acf-option-page' ) && Eac_Config_Elements::is_feature_active( 'grant-option-page' );
		$role_editor       = get_role( 'editor' );
		$role_shop_manager = get_role( 'shop_manager' );

		if ( $grant_option_page ) {
			if ( false === $role_editor->has_cap( Eac_Config_Elements::get_manage_options_name() ) ) {
				wp_roles()->add_cap( 'editor', Eac_Config_Elements::get_manage_options_name() );
			}

			if ( ! is_null( $role_shop_manager ) && false === $role_shop_manager->has_cap( Eac_Config_Elements::get_manage_options_name() ) ) {
				wp_roles()->add_cap( 'shop_manager', Eac_Config_Elements::get_manage_options_name() );
			}
		} else {
			if ( true === $role_editor->has_cap( Eac_Config_Elements::get_manage_options_name() ) ) {
				wp_roles()->remove_cap( 'editor', Eac_Config_Elements::get_manage_options_name() );
			}

			if ( ! is_null( $role_shop_manager ) && true === $role_shop_manager->has_cap( Eac_Config_Elements::get_manage_options_name() ) ) {
				wp_roles()->remove_cap( 'shop_manager', Eac_Config_Elements::get_manage_options_name() );
			}
		}
	}

	/**
	 * get_script_url
	 *
	 * Construit le chemin du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @return String Chemin absolu du fichier JS passé en paramètre
	 */
	public function get_script_url( $file ): string {
		return esc_url( EAC_PLUGIN_URL . $file . $this->suffix_js );
	}

	/**
	 * get_style_url
	 *
	 * Construit le chemin du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @param mixed $file
	 *
	 * @return String Chemin absolu du fichier CSS passé en paramètre
	 */
	public function get_style_url( $file ): string {
			return esc_url( EAC_PLUGIN_URL . $file . $this->suffix_css );
	}

	/**
	 * add_script_module_attribute
	 *
	 * Ajout de l'attribut type="module"
	 *
	 * @param mixed $tag Le contenu script
	 * @param mixed $handle l'ID du script
	 * @param mixed $src Le chemin du script
	 *
	 * @return string
	 */
	public function add_script_module_attribute( $tag, $handle, $src ): string {
		$module_scripts = array( 'instant-page', 'eac-acf-relation', 'eac-image-gallery', 'eac-advanced-gallery', 'eac-post-grid', 'eac-rss-reader', 'eac-news-ticker', 'eac-pinterest-rss' );

		if ( in_array( $handle, $module_scripts, true ) ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}
		return $tag;
	}

	/**
	 * add_style_attribute
	 *
	 * @param mixed $html
	 * @param mixed $handle
	 *
	 * @return [type]
	 */
	public function add_style_attribute( $html, $handle ): string {
		$module_styles = array( 'eac-fancybox', 'elegant-icons', 'eac-nav-menu' );

		if ( in_array( $handle, $module_styles, true ) ) {
			$html = str_replace( 'media=\'all\'', 'media=\'print\' onload="this.onload=null;this.media=\'all\'"', $html );
		}
		return $html;
	}

	/**
	 * register_script
	 *
	 * @param mixed $handle
	 * @param mixed $src
	 * @param mixed $deps
	 * @param mixed $ver
	 * @param array $args
	 *
	 * @return void
	 */
	public function register_script( $handle, $src, $deps, $ver, $args = array() ): void {
		global $wp_version;
		$url = $this->get_script_url( $src );

		// If WP >= 6.3, re-use wrapper function signature.
		if ( version_compare( $wp_version, '6.3', '>=' ) ) {
			wp_register_script(
				$handle,
				$url,
				$deps,
				$ver,
				$args
			);
		} else {
			// Extract in_footer value for older version usage.
			$in_footer = isset( $args['in_footer'] ) ? $args['in_footer'] : true;

			wp_register_script(
				$handle,
				$url,
				$deps,
				$ver,
				$in_footer
			);
		}
	}
}
