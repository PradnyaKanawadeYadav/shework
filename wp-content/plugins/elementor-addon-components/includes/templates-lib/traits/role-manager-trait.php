<?php
/** @since 2.2.9 Création du trait */

namespace EACCustomWidgets\Includes\TemplatesLib\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait EAC_Elementor_Restriction {

	public function get_elementor_restriction(): bool {
		$user = wp_get_current_user();
		/**$exclude_roles = get_option( 'elementor_exclude_user_roles', [] );
		$compare_roles   = array_intersect( $user->roles, $exclude_roles );*/
		$compare_roles   = array_intersect( $user->roles, \Elementor\Plugin::$instance->role_manager->get_role_manager_options() );
		return empty( $compare_roles ) ? false : true;
	}
}
