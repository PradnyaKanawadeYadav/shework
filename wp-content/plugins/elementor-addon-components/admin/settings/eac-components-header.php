<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use EACCustomWidgets\Core\Eac_Config_Elements;
?>
<div class='eac-header-settings'>
	<div class='eac-header'>
		<div>
			<img class='eac-logo' src="<?php echo esc_url( EAC_PLUGIN_URL ) . 'admin/images/logos/eac-03.svg'; ?>" />
		</div>
		<div>
			<h1 class='eac-title-main'><?php esc_html_e( 'Elementor Addon Components', 'eac-components' ); ?></h1>
			<h2 class='eac-title-sub'><?php esc_html_e( "Ajouter des composants et des fonctionnalités avancées pour la version gratuite d'Elementor", 'eac-components' ); ?></h2>
			<p class='eac-title-version'>Version: <?php echo esc_attr( EAC_PLUGIN_VERSION ); ?></p>
		</div>
	</div>
	<div class='eac-stat'>
		<div class='eac-stat__item'>
			<p class='eac-stat__count'><?php echo absint( Eac_Config_Elements::get_count_all_elements() ); ?></p>
			<h2 class='eac-stat__title'><?php esc_html_e( 'Composants', 'eac-components' ); ?></h2>
		</div>
		<div class='eac-stat__item'>
			<p class='eac-stat__count'><?php echo absint( Eac_Config_Elements::get_count_enabled_elements() ); ?></p>
			<h2 class='eac-stat__title'><?php esc_html_e( 'Composants actifs', 'eac-components' ); ?></h2>
		</div>
		<div class='eac-stat__item'>
		<p class='eac-stat__count'><?php echo absint( Eac_Config_Elements::get_count_disabled_elements() ); ?></p>
			<h2 class='eac-stat__title'><?php esc_html_e( 'Composants inactifs', 'eac-components' ); ?></h2>
		</div>
	</div>
</div>
