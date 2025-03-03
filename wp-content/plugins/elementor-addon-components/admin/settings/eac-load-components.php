<?php
/**
 * Description: Gère l'interface d'administration des composantrs EAC 'EAC Components'
 * et des options de la BDD.
 * Cette class est instanciée dans 'plugin.php' par le rôle administrateur.
 *
 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants.
 * Ajoute l'item 'EAC Components' dans le menu de la barre latérale
 * Charge le formulaire HTML de la page d'admin.
 *
 * @since 1.0.0
 */

namespace EACCustomWidgets\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

class EAC_Admin_Settings {

	/**
	 * $options_elements
	 *
	 * @var string
	 */
	private $options_elements = '';

	/**
	 * $wc_integration_nonce
	 * nonce pour le formulaire d'intégration WC
	 *
	 * @var string
	 */
	private $wc_integration_nonce = 'eac_settings_wc_integration_nonce';

	/**
	 * $elements_nonce
	 * nonce pour le formulaire global
	 *
	 * @var string
	 */
	private $elements_nonce = 'eac_settings_elements_nonce';

	/**
	 * $elements_count_nonce
	 *
	 * @var string
	 */
	private $elements_count_nonce = 'elements_count_nonce';

	/**
	 * $elements_keys
	 * La liste de tous les éléments par leur slug
	 *
	 * @var array
	 */
	private $elements_keys = array();

	/** L'instance de la class */
	private static $instance = null;

	/**
	 * __construct
	 *
	 */
	private function __construct() {

		/** Le libellé des options de la BDD */
		$this->options_elements = Eac_Config_Elements::get_elements_option_name();

		/** Affecte le tableau des éléments */
		$this->elements_keys = Eac_Config_Elements::get_elements_active();

		/** Enregistre les actions de création du sous-menu et de sauvegarde des formulaires */
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_scripts' ) );
		add_action( 'wp_ajax_save_elements', array( $this, 'save_elements' ) );
		add_action( 'wp_ajax_save_wc_integration', array( $this, 'save_wc_integration' ) );
	}

	/** Singleton de la class */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * admin_menu
	 *
	 * Création du nouveau menu dans le dashboard
	 */
	public function admin_menu() {
		$plugin_name = esc_html__( 'EAC composants', 'eac-components' );
		$option      = '';

		$current_user = wp_get_current_user();
		if ( $current_user->has_cap( Eac_Config_Elements::get_manage_options_name() ) ) {
			$option = Eac_Config_Elements::get_manage_options_name();
		} elseif ( $current_user->has_cap( 'manage_options' ) ) {
			$option = 'manage_options';
		}

		if ( ! empty( $option ) ) {
			add_menu_page( $plugin_name, $plugin_name, $option, EAC_DOMAIN_NAME, array( $this, 'admin_page' ), 'dashicons-admin-tools', '58.9' );
		}
	}

	/**
	 * admin_page_scripts
	 *
	 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants
	 */
	public function admin_page_scripts() {
		/** Le script de la page de configuration du plugin */
		wp_enqueue_script( 'eac-admin', EAC_Plugin::instance()->get_script_url( 'admin/js/eac-admin' ), array( 'jquery', 'jquery-ui-dialog' ), EAC_PLUGIN_VERSION, true );
		/** Le style de la page de configuration du plugin */
		wp_enqueue_style( 'eac-admin', EAC_Plugin::instance()->get_style_url( 'admin/css/eac-admin' ), array(), '1.0.0' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}

	/**
	 * admin_page
	 *
	 * Passe les paramètres au script 'eac-admin => eac-admin.js'
	 * Charge les templates de la page d'administration
	 */
	public function admin_page() {
		/** Options intégration WC */
		$settings_wc_integration = array(
			'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_action' => 'save_wc_integration',
			'ajax_nonce'  => wp_create_nonce( $this->wc_integration_nonce ),
		);
		wp_add_inline_script( 'eac-admin', 'var wcintegration = ' . wp_json_encode( $settings_wc_integration ), 'before' );

		/** Options éléments */
		$settings_elements = array(
			'ajax_url'    => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_action' => 'save_elements',
			'ajax_nonce'  => wp_create_nonce( $this->elements_nonce ),
		);
		wp_add_inline_script( 'eac-admin', 'var settingsElements = ' . wp_json_encode( $settings_elements ), 'before' );

		/** Compte du nombre d'éléments dans la page de paramétrage */
		$elements_count = array(
			'ajax_url'     => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_content' => esc_url( EAC_PLUGIN_URL . 'admin/settings/eac-admin-popup-elements-count.php' ) . '?ajax_nonce=' . wp_create_nonce( $this->elements_count_nonce ),
		);
		wp_add_inline_script( 'eac-admin', 'var elementsCount = ' . wp_json_encode( $elements_count ), 'before' );

		/** Charge les templates */
		require_once 'eac-components-header.php';
		require_once 'eac-components-tabs-nav.php';
		?>
		<div class='tabs-stage'>
			<?php
			require_once 'eac-components-tab1.php';
			if ( Eac_Config_Elements::is_widget_active( 'woo-product-grid' ) ) {
				require_once 'eac-components-tab6.php';
			}
			require_once 'eac-components-tab7.php';
			?>
		</div>
		<?php
		require_once 'eac-admin-popup-acf.php';
		require_once 'eac-admin-popup-grant-option.php';
		require_once 'eac-admin-popup-grant-medias.php';
		require_once 'eac-admin-popup-elements-help.php';
	}

	/**
	 * save_elements
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les éléments et leur état dans la table Options de la BDD
	 *
	 * @return void
	 */
	public function save_elements() {
		// Vérification du nonce pour cette action
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), $this->elements_nonce ) ) {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (nonce)", 'eac-components' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'Vous ne pouvez pas modifier les réglages', 'eac-components' ) );
		}

		// Les champs 'fields' sélectionnés 'on' sont serialisés dans 'eac-admin.js'
		if ( isset( $_POST['fields'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $fields_on );
		} else {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (champs)", 'eac-components' ) );
		}

		$settings_keys = array();
		$keys          = array_keys( $this->elements_keys );

		// La liste des options de tous les composants activés
		foreach ( $keys as $key ) {
			$key                   = sanitize_text_field( $key );
			$settings_keys[ $key ] = boolval( isset( $fields_on[ $key ] ) ? 1 : 0 );
		}

		// Update de la BDD
		update_option( $this->options_elements, $settings_keys );

		// Met à jour les options pour le template template 'tab1'
		$this->elements_keys = get_option( $this->options_elements );

		// Supprime l'option de l'usage des éléménts
		delete_option( Eac_Config_Elements::get_usage_count_option_name() );

		// retourne 'success' au script JS
		wp_send_json_success( esc_html__( 'Réglages enregistrés', 'eac-components' ) );
	}

	/**
	 * save_wc_integration
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les options de l'intégration WC dans la table Options de la BDD
	 *
	 * @return void
	 */
	public function save_wc_integration() {
		$woo_shop_args = Eac_Config_Elements::get_woo_hooks_option_args();

		/** Vérification du nonce pour cette action */
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), $this->wc_integration_nonce ) ) {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (nonce)", 'eac-components' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'Vous ne pouvez pas modifier les réglages', 'eac-components' ) );
		}

		/** Les champs 'fields' sont serialisés dans 'eac-admin.js' */
		if ( isset( $_POST['fields'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $fields_on );
		} else {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (champs)", 'eac-components' ) );
		}

		/** WooCommerce n'est pas installé */
		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( esc_html__( "WooCommerce n'est pas installé/activé sur votre site", 'eac-components' ) );
		}

		/** ID et URL de la page grille de produit */
		if ( isset( $fields_on['wc_product_select_page'] ) && ! empty( $fields_on['wc_product_select_page'] ) ) {
			$woo_shop_args['product-page']['shop']['url'] = esc_url_raw( get_permalink( absint( $fields_on['wc_product_select_page'] ) ) );
			$woo_shop_args['product-page']['shop']['id']  = absint( $fields_on['wc_product_select_page'] );
		} else {
			$woo_shop_args['product-page']['shop']['url'] = '';
			$woo_shop_args['product-page']['shop']['id']  = (int) 0;
		}

		/**
		 * Les boutons de la page panier
		 * Les URLs du breadcrumb de la page product
		 * Les URLs des métas de la page produit
		 */
		if ( '' !== $woo_shop_args['product-page']['shop']['url'] ) {
			$woo_shop_args['product-page']['redirect_buttons']   = boolval( isset( $fields_on['wc_product_redirect_url'] ) ? 1 : 0 );
			$woo_shop_args['product-page']['breadcrumb']         = boolval( isset( $fields_on['wc_product_breadcrumb'] ) ? 1 : 0 );
			$woo_shop_args['product-page']['metas']              = boolval( isset( $fields_on['wc_product_metas'] ) ? 1 : 0 );
		} else {
			$woo_shop_args['product-page']['redirect_buttons']   = boolval( 0 );
			$woo_shop_args['product-page']['breadcrumb']         = boolval( 0 );
			$woo_shop_args['product-page']['metas']              = boolval( 0 );
		}

		/** Le site en catalogue */
		$woo_shop_args['catalog']['active'] = boolval( isset( $fields_on['wc_product_catalog'] ) ? 1 : 0 );

		/** Message dans la page du produit 'request a quote' et redirection des pages */
		if ( $woo_shop_args['catalog']['active'] ) {
			$woo_shop_args['catalog']['request_quote'] = boolval( isset( $fields_on['wc_product_request'] ) ? 1 : 0 );
			if ( '' !== $woo_shop_args['product-page']['shop']['url'] ) {
				$woo_shop_args['redirect_pages'] = boolval( isset( $fields_on['wc_product_redirect_pages'] ) ? 1 : 0 );
			} else {
				$woo_shop_args['redirect_pages'] = boolval( 0 );
			}
		} else {
			$woo_shop_args['catalog']['request_quote'] = boolval( 0 );
			$woo_shop_args['redirect_pages']           = boolval( 0 );
		}

		/** Update de la BDD */
		update_option( Eac_Config_Elements::get_woo_hooks_option_name(), $woo_shop_args );

		/** retourne 'success' au script JS */
		wp_send_json_success( esc_html__( 'Réglages enregistrés', 'eac-components' ) );
	}
} EAC_Admin_Settings::instance();
