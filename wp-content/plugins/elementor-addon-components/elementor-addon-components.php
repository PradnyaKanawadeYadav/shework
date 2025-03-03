<?php
/**
 * Plugin Name: Elementor Addon Components
 * Description: Ce plugin étonnant comprend des composants gratuits, tels que les grilles d'images, d'articles et de produits. Un constructeur d'en-tête et de pied de page, des CSS personnalisés, des balises dynamiques dont ACF, des conditions d'affichage des éléments et bien plus encore.
 * Plugin URI: https://elementor-addon-components.com/
 * Author: Team EAC
 * Version: 2.3.3
 * Requires at least: 6.3.0
 * Tested up to: 6.7.2
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * Elementor tested up to: 3.27.6
 * WC requires at least: 8.0.0
 * WC tested up to: 9.4.2
 * Author URI: https://elementor-addon-components.com/
 * Text Domain: eac-components
 * Domain Path: /languages
 * License: GPLv3 or later License
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 'Elementor Addon Components' is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GPL General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'EAC_DOMAIN_NAME', 'eac-components' );
define( 'EAC_PLUGIN_NAME', 'Elementor Addon Components' );
define( 'EAC_PLUGIN_SLUG', 'elementor-addon-components' );
define( 'EAC_PLUGIN_SITE', 'https://elementor-addon-components.com' );
define( 'EAC_PLUGIN_VERSION', '2.3.3' );
define( 'EAC_PLUGIN_FILE', __FILE__ );
define( 'EAC_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'EAC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EAC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'EAC_ELEMENTOR_INCLUDES', EAC_PLUGIN_PATH . 'includes/elementor/' );
define( 'EAC_ACF_INCLUDES', EAC_PLUGIN_PATH . 'includes/acf/' );
define( 'EAC_ACF_JSON_PATH', EAC_PLUGIN_PATH . 'includes/acf/acf-json' );
define( 'EAC_WC_INCLUDES', EAC_PLUGIN_PATH . 'includes/woocommerce/' );
define( 'EAC_CONDITION_PATH', EAC_PLUGIN_PATH . 'includes/display-conditions/' );
define( 'EAC_WIDGETS_PATH', EAC_PLUGIN_PATH . 'includes/widgets/' );
define( 'EAC_WIDGETS_NAMESPACE', 'EACCustomWidgets\\Includes\\Widgets\\' );
define( 'EAC_EHF_PATH', EAC_PLUGIN_PATH . 'includes/templates-lib/' );
define( 'EAC_EHF_WIDGETS_NAMESPACE', 'EACCustomWidgets\\Includes\\TemplatesLib\\Widgets\\' );

define( 'EAC_SCRIPT_DEBUG', false ); // true = .js ou false = .min.js
define( 'EAC_STYLE_DEBUG', false );  // true = .css ou false = .min.css

function eac_plugin_activation() {
	update_option( 'eac_options_plugin_activated', 'yes' );
}
register_activation_hook( EAC_PLUGIN_FILE, 'eac_plugin_activation' );

function eac_plugin_deactivation() {
	update_option( 'eac_options_plugin_activated', 'no' );
}
register_deactivation_hook( EAC_PLUGIN_FILE, 'eac_plugin_deactivation' );

/**
 * Charge le language
 * @since 2.2.9 Fix _load_textdomain_just_in_time WP 6.7
 */
function eac_load_language() {
	$file_name = sprintf( '%s-%s.mo', EAC_DOMAIN_NAME, get_user_locale() );
	$path_file = EAC_PLUGIN_PATH . 'languages/' . $file_name;
	if ( 'fr_FR' !== get_user_locale() && ! file_exists( $path_file ) ) {
			$file_name = sprintf( '%s-%s.mo', EAC_DOMAIN_NAME, 'en_US' );
			$path_file = EAC_PLUGIN_PATH . 'languages/' . $file_name;
	}
	load_textdomain( EAC_DOMAIN_NAME, $path_file );
}
add_action( 'init', 'eac_load_language' );

/** Vérifie les compatibilités et instancie le plugin */
function eac_load_plugin() {
	if ( eac_is_compatible() ) {
		require_once __DIR__ . '/includes/eac-plugin.php';
		add_filter( 'plugin_action_links_' . EAC_PLUGIN_BASENAME, 'eac_add_settings_action_links' );
		add_filter( 'plugin_row_meta', 'eac_add_row_meta_links', 10, 2 );

		\EACCustomWidgets\EAC_Plugin::instance();
	} else {
		deactivate_plugins( EAC_PLUGIN_BASENAME );
	}
}
add_action( 'plugins_loaded', 'eac_load_plugin' );

/** Vérification des compatibilités */
function eac_is_compatible() {
	$compatible        = true;
	$elementor_version = '3.5.6';
	$wp_version        = '6.3.0';
	$php_version       = '7.4';

	/** Notification Elementor n'est pas chargé */
	if ( ! did_action( 'elementor/loaded' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function() { ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php esc_html_e( 'Elementor Addon Components ne fonctionne pas car vous devez activer le plugin Elementor !', 'eac-components' ); ?></p>
				</div>
			<?php }
		);
		/** Notification Elementor n'est pas à la bonne version */
	} elseif ( version_compare( ELEMENTOR_VERSION, $elementor_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function() use ( $elementor_version ) {
				$message = sprintf(
					/* translators: 1: Elementor version minimum */
					esc_html__( 'Elementor Addon Components version minimale Elementor', 'eac-components' ) . ' %1$s',
					$elementor_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
		/** Notification WordPress n'est pas à la bonne version */
	} elseif ( version_compare( get_bloginfo( 'version' ), $wp_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function() use ( $wp_version ) {
				$message = sprintf(
					/* translators: 1: WordPress version minimum */
					esc_html__( 'Elementor Addon Components version minimale WordPress', 'eac-components' ) . ' %1$s',
					$wp_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
		/** Notification PHP n'est pas à la bonne version */
	} elseif ( version_compare( PHP_VERSION, $php_version, '<' ) ) {
		$compatible = false;
		add_action(
			'admin_notices',
			function() use ( $php_version ) {
				$message = sprintf(
					/* translators: 1: PHP version minimum */
					esc_html__( 'Elementor Addon Components version minimale PHP', 'eac-components' ) . ' %1$s',
					$php_version
				); ?>
				<div class='notice notice-error is-dismissible'>
					<p><?php echo esc_html( $message ); ?></p>
				</div>
			<?php } );
	}
	return $compatible;
}

/** Ajout du lien vers la page de réglages du plugin */
function eac_add_settings_action_links( $links ) {
	$settings_link = array( '<a href="' . esc_url( admin_url( 'admin.php?page=eac-components' ) ) . '">' . esc_html__( 'Réglages', 'eac-components' ) . '</a>' );
	return array_merge( $settings_link, $links );
}

/** Ajout du lien vers la page du centre d'aide et voir les détails du plugin */
function eac_add_row_meta_links( $meta_links, $plugin_file ) {
	if ( EAC_PLUGIN_BASENAME === $plugin_file ) {
		// Lien view détails
		$meta_links[2] = sprintf(
			'<a href="%1$s" class="thickbox open-plugin-details-modal">%2$s</a>',
			esc_url( add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => EAC_PLUGIN_SLUG,
					'TB_iframe' => true,
					'width'     => 600,
					'height'    => 550,
				),
				admin_url( 'plugin-install.php' )
			) ),
			esc_html__( 'Voir les détails', 'eac-components' )
		);

		// Help Center
		$settings_link = array(
			'<a href="' . EAC_PLUGIN_SITE . '/help-center/" target="_blank" rel="noopener noreferrer">' . esc_html__( "Centre d'aide", 'eac-components' ) . '</a>',
			/**'<a href="' . EAC_PLUGIN_SITE . '/donate.php?for=' . EAC_PLUGIN_SLUG . '" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-smiley"></span> <span>Buy me a coffee!</span></a>'*/
		);
		$meta_links    = array_merge( $meta_links, $settings_link );
	}
	return $meta_links;
}
