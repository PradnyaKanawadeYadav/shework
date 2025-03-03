<?php
/**
 * Page d'information système
 *
 * @since 2.2.8
 */

namespace EACCustomWidgets\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id='tab-7' style='display: none;'>
	<table class='widefat' cellspacing='0'>
		<thead><tr><th colspan='2'><b><?php echo esc_html( 'WordPress Environment' ); ?></b></th></tr></thead>
		<tbody>
			<tr>
				<td><?php echo esc_html( 'Home URL' ); ?></td>
				<td><?php form_option( 'home' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Site URL' ); ?></td>
				<td><?php form_option( 'siteurl' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'WP Path' ); ?></td>
				<td><?php echo ABSPATH; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'WP Version' ); ?></td>
				<td><?php bloginfo( 'version' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'WP Multisite' ); ?></td>
				<td><?php is_multisite() ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'WP Memory Limit' ); ?></td>
				<td><?php echo esc_html( size_format( wp_convert_hr_to_bytes( (string) WP_MEMORY_LIMIT ) ) ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'WP Debug Mode' ); ?></td>
				<td><?php defined( 'WP_DEBUG' ) && WP_DEBUG ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Admin Email' ); ?></td>
				<td><?php form_option( 'admin_email' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Language' ); ?></td>
				<td><?php echo esc_html( get_locale() ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Fuseau horaire', 'eac-components' ); ?></td>
				<td><?php echo esc_html( wp_timezone_string() ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'GMT Offset' ); ?></td>
				<td><?php form_option( 'gmt_offset' ); ?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class='widefat' cellspacing='0'>
		<thead><tr><th colspan='2'><b><?php echo esc_html( 'Server Environment' ); ?></b></th></tr></thead>
		<tbody>
			<tr>
				<td><?php echo esc_html( 'Address IP' ); ?></td>
				<td>
					<?php
					$server_ip_address = ! empty( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
					if ( '' === $server_ip_address ) {
						$server_ip_address = ! empty( $_SERVER['LOCAL_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['LOCAL_ADDR'] ) ) : '';
					}
					echo esc_html( $server_ip_address );
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Serveur Info' ); ?></td>
				<td><?php echo esc_html( isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'PHP Version' ); ?></td>
				<td>
					<?php
					$php_version             = PHP_VERSION;
					$recommended_php_version = '7.4';
					if ( version_compare( $php_version, $recommended_php_version, '<' ) ) {
						echo '<span style="color: red; font-size: 17px; font-weight: 600;">' . esc_html( $php_version ) . '!!</span>';
					} else {
						echo esc_html( $php_version );
					}
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'MySQL Version' ); ?></td>
				<td>
				<?php
				global $wpdb;
				echo esc_html( $wpdb->db_version() );
				?>
				</td>
			</tr>
			<?php if ( function_exists( 'ini_get' ) ) : ?>
				<tr>
					<td><?php echo esc_html( 'PHP Memory Limit' ); ?></td>
					<td>
						<?php
						$php_memory = wp_convert_hr_to_bytes( (string) ini_get( 'memory_limit' ) );
						$recommended_memory = wp_convert_hr_to_bytes( '128M' );
						if ( $php_memory < $recommended_memory ) {
							echo '<span style="color: red; font-size: 17px; font-weight: 600;">' . esc_html( size_format( $php_memory ) ) . '!!</span>';
						} else {
							echo esc_html( size_format( $php_memory ) );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php echo esc_html( 'PHP Post Max Size' ); ?></td>
					<td><?php echo esc_html( size_format( wp_convert_hr_to_bytes( (string) ini_get( 'post_max_size' ) ) ) ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html( 'PHP Time Limit' ); ?></td>
					<td><?php echo esc_html( ini_get( 'max_execution_time' ) ) . 's'; ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html( 'PHP Max Input Vars' ); ?></td>
					<td><?php echo esc_html( ini_get( 'max_input_vars' ) ); ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td><?php echo esc_html( 'Max Upload Size' ); ?></td>
			<td><?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></td>
			</tr>
			</tbody>
	</table>
	<br>
	<table class='widefat' cellspacing='0'>
		<thead><tr><th colspan='2'><b><?php echo esc_html( 'PHP Extensions' ); ?></b></th></tr></thead>
		<tbody>
			<tr>
				<td><?php echo esc_html( 'cURL' ); ?></td>
				<td><?php echo ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'fsockopen' ); ?></td>
				<td><?php echo ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'SOAP Client' ); ?></td>
				<td><?php echo ( class_exists( 'SoapClient' ) ? 'Supported' : 'Not Supported' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Zlib' ); ?></td>
				<td><?php filter_var( extension_loaded( 'zip' ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Zlib Output Compression' ); ?></td>
				<?php $oui = '<span style="color: red; font-size: 17px; font-weight: 600;">' . esc_html__( 'Oui', 'eac-components' ) . '</span>'; ?>
				<?php $non = esc_html__( 'Non', 'eac-components' ); ?>
				<td><?php echo filter_var( ini_get( 'zlib.output_compression' ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? $oui : $non; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'GD' ); ?></td>
				<td><?php filter_var( extension_loaded( 'gd' ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'XML' ); ?></td>
				<td><?php class_exists( 'DomDocument' ) ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Simplexml' ); ?></td>
				<td><?php filter_var( extension_loaded( 'simplexml' ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class='widefat' cellspacing='0'>
		<?php $the_theme = wp_get_theme(); ?>
		<thead><tr><th colspan='2'><b><?php echo esc_html( 'Theme' ); ?></b></th></tr></thead>
		<tbody>
			<tr>
				<td><?php esc_html_e( 'Nom', 'eac-components' ); ?></td>
				<td><?php echo esc_html( $the_theme ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Version', 'eac-components' ); ?></td>
				<td><?php echo esc_html( $the_theme->get( 'Version' ) ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Auteur', 'eac-components' ); ?></td>
				<td><a target='autre' rel='nofollow noopener noreferrer' href="<?php echo esc_url( $the_theme->get( 'ThemeURI' ) ); ?>"><?php echo esc_html( $the_theme->display( 'Author', false ) ); ?></a></td>
			</tr>
			<tr>
				<td><?php echo esc_html( 'Child Theme' ); ?></td>
				<td><?php echo filter_var( is_child_theme(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? esc_html_e( 'Oui', 'eac-components' ) : esc_html_e( 'Non', 'eac-components' ); ?></td>
			</tr>
		</tbody>
	</table>
	<br>
	<table class='widefat' cellspacing='0'>
		<thead><tr><th colspan='2'><b><?php echo esc_html( 'Active Plugins' ); ?> (<?php echo absint( count( (array) get_option( 'active_plugins' ) ) ); ?>)</b></th></tr></thead>
		<tbody>
			<?php
			$active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
				$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
			}
			foreach ( $active_plugins as $the_plugin ) {
				$plugin_data    = get_plugin_data( WP_PLUGIN_DIR . '/' . $the_plugin );
				$dirname        = dirname( $the_plugin );
				$version_string = '';
				$network_string = '';

				if ( ! empty( $plugin_data['Name'] ) ) {
					$plugin_name = esc_html( $plugin_data['Name'] );
					if ( ! empty( $plugin_data['PluginURI'] ) ) {
						$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Page du plugin', 'eac-components' ) . '" target="autre" rel="nofollow noopener noreferrer">' . $plugin_name . ' &ndash; v' . esc_html( $plugin_data['Version'] ) . '</a>';
					}
					?>
					<tr>
						<td><?php echo wp_kses_post( $plugin_name ); ?></td>
						<td><?php printf( esc_html_x( 'par %s', 'par auteur', 'eac-components' ), wp_kses_post( $plugin_data['Author'] ) ) . wp_kses_post( $version_string ) . wp_kses_post( $network_string ); ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</div>
