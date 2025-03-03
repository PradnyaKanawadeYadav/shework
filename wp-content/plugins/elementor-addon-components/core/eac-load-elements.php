<?php
/**
 * Class: Eac_Load_Elements
 *
 * Description: Charge les groups, controls et les composants actifs Pour Elementor
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

class Eac_Load_Elements {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * Constructeur de la class
	 *
	 * Ajoute les actions pour enregsitrer les goupes, controls et widgets Elementor
	 *
	 * @param $elements La liste des composants et leur état
	 */
	private function __construct() {

		/** Les actions AJAX 'wp_ajax_xxxxxx' pour le control 'eac-select2' doivent être chargées avant les actions Elementor */
		require_once EAC_PLUGIN_PATH . 'includes/elementor/controls/eac-select2-actions.php';

		/** Filtres WooCommerce. Le mega menu intègre le filtre 'woocommerce_add_to_cart_fragments' pour le mini-cart */
		if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) || Eac_Config_Elements::is_widget_active( 'mega-menu' ) ) {
			require_once EAC_PLUGIN_PATH . 'includes/woocommerce/eac-woo-hooks.php';
		} else {
			// On force la suppression de l'option des filtres WC par sécurité
			delete_option( Eac_Config_Elements::get_woo_hooks_option_name() );
		}

		/** Charge les traits avant le module Header & Footer et les widgets */
		$this->load_traits();

		/** Initialize le module Header & Footer avant les actions Elementor */
		if ( Eac_Config_Elements::is_widget_active( 'header-footer' ) ) {
			$path = Eac_Config_Elements::get_widget_path( 'header-footer' );
			if ( $path ) {
				// Charge les actions AJAX 'wp_ajax_xxxxxx' pour mettre à jour le badge du mini-cart
				require_once EAC_PLUGIN_PATH . 'includes/templates-lib/widgets/classes/class-menu-actions.php';
				require_once $path;
			}
		}

		/** Création des catégories de composants */
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );

		/** Charge les controls. Enregistre les class des controls */
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );

		/** Charge les widgets. Enregistre les class des composants */
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/** Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Crée les catégories des composants */
	public function register_categories( $elements_manager ) {
		$elements_manager->add_category(
			'eac-advanced',
			array(
				'title' => esc_html__( 'EAC Avancés', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-basic',
			array(
				'title' => esc_html__( 'EAC Basiques', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
		$elements_manager->add_category(
			'eac-ehf',
			array(
				'title' => esc_html__( 'EAC Entête & Pied de page', 'eac-components' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Enregistre les nouveaux controls
	 *
	 * @args $controls_manager Gestionnaire des controls
	 */
	public function register_controls( $controls_manager ) {

		// Enregistre le control 'file-viewer' pour le composant 'PDF viewer'
		require_once EAC_PLUGIN_PATH . 'includes/elementor/controls/file-viewer-control.php';

		// Enregistre le control 'eac-select2' pour le control select2
		require_once EAC_PLUGIN_PATH . 'includes/elementor/controls/eac-select2-control.php';

		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\Simple_File_Viewer_Control() );
		$controls_manager->register( new \EACCustomWidgets\Includes\Elementor\Controls\Ajax_Select2_Control() );
	}

	/** Enregistre les traits ou les libs des widgets/fonctionnalités */
	public function load_traits() {
		/** Charge le trait pour la gestion des rôles d'Elementor 'role manager' */
		require_once EAC_EHF_PATH . 'traits/role-manager-trait.php';

		/**
		 * Le trait pour les titres de page avec le contexte
		 * Le dynamic tag page title et le widget page title de la fonctionnalité header-footer
		 */
		require_once EAC_WIDGETS_PATH . 'traits/page-title-trait.php';

		// Le trait pour les pitogrammes SVG
		if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) ||
		Eac_Config_Elements::is_widget_active( 'articles-liste' ) ||
		Eac_Config_Elements::is_widget_active( 'site-search' ) ||
		Eac_Config_Elements::is_widget_active( 'mega-menu' ) ) {
			require_once EAC_WIDGETS_PATH . 'traits/icon-svg-trait.php';
		}

		// Les traits pour les gilles
		if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) ||
		Eac_Config_Elements::is_widget_active( 'articles-liste' ) ||
		Eac_Config_Elements::is_widget_active( 'acf-relationship' ) ||
		Eac_Config_Elements::is_widget_active( 'image-galerie' ) ||
		Eac_Config_Elements::is_widget_active( 'advanced-gallery' ) ) {
			require_once EAC_WIDGETS_PATH . 'traits/slider-trait.php';
			require_once EAC_WIDGETS_PATH . 'traits/button-read-more-trait.php';
		}

		// Les traits pour le composant product grid
		if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) ) {
			require_once EAC_WIDGETS_PATH . 'traits/button-add-to-cart-trait.php';
			require_once EAC_WIDGETS_PATH . 'traits/badge-new-trait.php';
			require_once EAC_WIDGETS_PATH . 'traits/badge-sale-trait.php';
			require_once EAC_WIDGETS_PATH . 'traits/badge-stock-trait.php';
		}
	}

	/** Enregistre les composants actifs */
	public function register_widgets( $widgets_manager ) {
		foreach ( Eac_Config_Elements::get_widgets_active() as $element => $active ) {
			if ( Eac_Config_Elements::is_widget_active( $element ) ) {
				$path       = Eac_Config_Elements::get_widget_path( $element );
				$name_space = Eac_Config_Elements::get_widget_namespace( $element );
				if ( $path && ! empty( $name_space ) ) {
					require_once $path;
					$widgets_manager->register( new $name_space() );
				}
			}
		}
	}

} Eac_Load_Elements::instance();
