<?php
/**
 * Class: Eac_Load_Nav_Menu
 *
 * Description: Création et ajout du bouton de chargement du template du menu
 *              Filtre sur le titre de chaque item de menu
 *              Sauvegarde dans la BDD du Meta de chaque item de menu
 *
 * @since 1.9.6
 */

namespace EACCustomWidgets\Admin\Settings;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\EAC_Plugin;

use Elementor\Icons_Manager;

class Eac_Load_Nav_Menu {

	/**
	 * @var $meta_item_menu_name
	 *
	 * Le nom du Meta pour la sauvegarde des données du formulaire d'un item de menu
	 */
	private $meta_item_menu_name = '_eac_custom_nav_menu_item';

	/**
	 * @var $menu_nonce
	 *
	 * Le nonce pour la sauvegarde du formulaire
	 */
	private $menu_nonce = 'eac_settings_menu_nonce';

	/**
	 * @var $menu_url_nonce
	 *
	 * Le nonce pour l'ouverture du formulaire
	 */
	private $menu_url_nonce = 'eac_settings_menu_url_nonce';

	/**
	 * Constructeur de la class
	 */
	public function __construct() {

		/** L'option de modification du menu est actif */
		if ( Eac_Config_Elements::is_feature_active( 'custom-nav-menu' ) ) {
			/** Bouton de chargement du template de menu */
			if ( current_user_can( 'manage_options' ) ) {
				add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_menu_item_fields' ), 10, 2 );
			}

			/**
			 * Filtre sur chaque titre d'un item du menu
			 * Priorité 9 pour déclencher avant les filtres des themes de leurs Walker
			 */
			add_filter( 'nav_menu_item_title', array( $this, 'update_nav_menu_title' ), 9, 4 );

			// Scripts et action pour les champs template du menu dans l'administration
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'wp_ajax_save_menu_settings', array( $this, 'save_menu_settings' ) );

			// Styles du frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_styles' ) );
		}
	}

	/**
	 * admin_enqueue_scripts
	 *
	 * Ajout des styles et des scripts pour les nouveaux champs du menu dans l'administration
	 */
	public function admin_enqueue_scripts() {
		/** Fonctionnalité Custom nav menu activée */
		// Gestionnaire du CSS/JS color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

			// Gestionnaire des medias
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}

		// Ajout JS/CSS de la Fancybox
		EAC_Plugin::instance()->register_script( 'eac-fancybox', 'assets/js/fancybox/jquery.fancybox', array( 'jquery' ), '3.5.7',
			array(
				'strategy' => 'defer',
				'in_footer' => true,
			)
		);
		wp_enqueue_script( 'eac-fancybox' );
		wp_enqueue_style( 'eac-fancybox', EAC_PLUGIN_URL . 'assets/css/jquery.fancybox.min.css', array(), '3.5.7' );

		// Ajout JS/CSS de gestion des événements de la Fancybox pour le menu de navigation
		wp_enqueue_script( 'eac-admin-nav-menu', EAC_Plugin::instance()->get_script_url( 'admin/js/eac-admin-nav-menu' ), array( 'jquery', 'wp-color-picker' ), EAC_PLUGIN_VERSION, true );
		wp_enqueue_style( 'eac-admin-nav-menu', EAC_Plugin::instance()->get_style_url( 'admin/css/eac-admin-nav-menu' ), array(), '1.9.6' );

		// Elegant icons
		wp_enqueue_style( 'elegant-icons', EAC_Plugin::instance()->get_style_url( 'admin/css/elegant-icons' ), array(), '1.3.3' );

		// Ajout du JS/CSS fontIconPicker
		wp_enqueue_script( 'font-icon-picker', EAC_PLUGIN_URL . 'admin/js/jquery.fonticonpicker.min.js', array( 'jquery' ), '3.1.1', true );
		wp_enqueue_script( 'eac-icon-lists', EAC_Plugin::instance()->get_script_url( 'admin/js/eac-icon-lists' ), array(), '1.9.6', true );
		wp_enqueue_style( 'eac-icon-picker', EAC_PLUGIN_URL . 'admin/css/jquery.fonticonpicker.min.css', array(), '3.1.1' );
		wp_enqueue_style( 'font-icon-picker-style', EAC_PLUGIN_URL . 'admin/css/jquery.fonticonpicker.grey.min.css', array(), '3.1.1' );

		/** Les font awesome ne sont pas chargées dans le dashboard */
		if ( ! wp_style_is( 'font-awesome-5-all', 'enqueued' ) ) {
			wp_enqueue_style( 'font-awesome-5-all', plugins_url( '/elementor/assets/lib/font-awesome/css/all.min.css' ), array(), '5.15.3' );
		}

		// Paramètres passés au script Ajax 'eac-admin-nav-menu'
		$settings_menu = array(
			'ajax_url'     => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_action'  => 'save_menu_settings',
			'ajax_nonce'   => wp_create_nonce( $this->menu_nonce ),
			'ajax_content' => esc_url( EAC_PLUGIN_URL . 'admin/settings/eac-admin-popup-menu.php' ) . '?nonce=' . wp_create_nonce( $this->menu_url_nonce ) . '&item_id=',
		);
		wp_add_inline_script( 'eac-admin-nav-menu', 'var menu = ' . wp_json_encode( $settings_menu ), 'before' );

	}

	/**
	 * wp_enqueue_styles
	 *
	 * Ajout des styles pour les icones du menu dans le frontend
	 */
	public function front_enqueue_styles() {

		// Les dashicons
		if ( ! wp_style_is( 'dashicons', 'enqueued' ) ) {
			wp_enqueue_style( 'dashicons' );
		}

		// Elegant icons
		if ( ! wp_style_is( 'elegant-icons', 'enqueued' ) ) {
			wp_enqueue_style( 'elegant-icons', EAC_Plugin::instance()->get_style_url( 'admin/css/elegant-icons' ), array(), '1.3.3' );
		}

		// Les styles de la fonctionnalité
		wp_enqueue_style( 'eac-nav-menu', EAC_Plugin::instance()->get_style_url( 'admin/css/eac-nav-menu' ), array( 'eac-frontend' ), '1.9.6' );
	}

	/**
	 * update_nav_menu_title
	 *
	 * Ajout des classes à chaque titre du menu avant d'être affiché
	 */
	public function update_nav_menu_title( $title, $item, $args, $depth ) {

		$menu_meta = get_post_meta( (int) $item->ID, $this->meta_item_menu_name, true );
		if ( empty( $title ) || empty( $menu_meta ) ) {
			return $title;
		}

		$theme          = strtolower( esc_html( get_template() ) );
		$icon           = '';
		$meta_icon      = $menu_meta['icon'];
		$badge          = '';
		$meta_badge     = $menu_meta['badge']['content'];
		$thumb          = '';
		$meta_thumb     = isset( $menu_meta['thumbnail']['state'] ) ? $menu_meta['thumbnail']['state'] : $menu_meta['thumbnail'];
		$image          = '';
		$meta_image_url = $menu_meta['image']['url'];
		$classes        = array( 'nav-menu_title-container depth-' . $depth . ' ' . $theme );
		$processed      = false;
		$has_children   = false;

		// Pas d'icone, pas de badge, pas de miniature et pas d'image
		if ( empty( $meta_icon ) && empty( $meta_badge ) && empty( $meta_thumb ) && empty( $meta_image_url ) ) {
			return $title;
		}

		// Ajout des classes pour les items qui ont un enfant
		if ( isset( $args->container_class ) ) {
			foreach ( $item->classes as $classe ) {
				if ( 'menu-item-has-children' === $classe ) {
					$classes      = array( 'nav-menu_title-container has-children depth-' . $depth . ' ' . $theme );
					$has_children = true;
				}
			}
		}

		/**
		 * Cache en bloc les éléments ajoutés dans un menu
		 * 'hide-main'      Cache les éléments du menu principal
		 * 'hide-widget'    Cache les éléments du menu affiché dans un widget
		 * 'hide-canvas'    Cache les éléments du menu affiché dans off-canvas
		 *
		 * @param array $classes Le tableau de class
		 */
		$class_names = join( ' ', apply_filters( 'eac_menu_item_class', $classes ) );

		// Ajout de l'image
		if ( ! empty( $meta_image_url ) ) {
			$image_size = $menu_meta['image']['sizes'];

			/**
			 * Filtre la largeur de l'image
			 *
			 * @param $image_size Largeur de l'image
			 */
			$image_size = apply_filters( 'eac_menu_image_size', $image_size );

			if ( empty( $image_size ) || is_array( $image_size ) ) {
				$image_size = 30;
			}

			$attachment_id = attachment_url_to_postid( $meta_image_url );
			$image_alt     = 0 !== $attachment_id && get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) : 'Nav menu item title ' . $attachment_id;
			$image         = '<img class="nav-menu_item-image" src="' . esc_url( $meta_image_url ) . '" style="width: ' . absint( $image_size ) . 'px; height:' . absint( $image_size ) . 'px;" alt="' . esc_attr( $image_alt ) . '" aria-hidden="true" />';
		}

		// Ajout de la miniature
		if ( ! empty( $meta_thumb ) ) {
			$sizes = isset( $menu_meta['thumbnail']['sizes'] ) ? $menu_meta['thumbnail']['sizes'] : 30;

			/**
			 * Filtre les dimensions de la miniature
			 *
			 * @param array $thumbnail_size Dimensions de la miniature
			 */
			$sizes = apply_filters( 'eac_menu_thumbnail_size', $sizes );

			if ( empty( $sizes ) || is_array( $sizes ) ) {
				$thumbnail_size = array( 30, 30 );
			} else {
				$thumbnail_size = array( absint( $sizes ), absint( $sizes ) );
			}

			$thumb = get_the_post_thumbnail(
				$item->object_id,
				$thumbnail_size,
				array(
					'class'       => 'nav-menu_item-thumb',
					'aria-hidden' => 'true',
				)
			);
		}

		// Ajout du picto
		if ( ! empty( $meta_icon ) ) {
			if ( preg_match( '#^fa.?\\s#', $meta_icon ) ) { // '#^(fa\\s|fas\\s|far\\s|fab\\s)#'
				$library = '';
				if ( 'fas' === substr( $meta_icon, 0, 3 ) ) {
					$library = 'fa-solid';
				} elseif ( 'far' === substr( $meta_icon, 0, 3 ) ) {
					$library = 'fa-regular';
				} elseif ( 'fab' === substr( $meta_icon, 0, 3 ) ) {
					$library = 'fa-brands';
				}

				$icon_array = array(
					'library' => $library,
					'value'   => $meta_icon,
				);

				ob_start();
				Icons_Manager::render_icon( $icon_array, array( 'aria-hidden' => 'true' ) );
				$icon = '<span class="nav-menu_item-icon eac-icon-svg">' . ob_get_clean() . '</span>';
			} else {
				$icon = '<span class="nav-menu_item-icon"><i class="' . esc_attr( $meta_icon ) . '" aria-hidden="true"></i></span>';
			}
		}

		// Ajout du badge
		if ( ! empty( $meta_badge ) ) {
			$menu_badge_color   = $menu_meta['badge']['color'];
			$menu_badge_bgcolor = $menu_meta['badge']['bgcolor'];
			$badge              = '<span class="nav-menu_item-badge" style="color:' . $menu_badge_color . '; background-color:' . $menu_badge_bgcolor . ';">' . $meta_badge . '</span>';
		}

		$item_title  = '<span class="' . esc_attr( $class_names ) . '">';
		$item_title .= $image;
		$item_title .= $thumb;
		$item_title .= $icon;
		$item_title .= '<span class="nav-menu_item-title">' . esc_html( $title ) . '</span>';
		$item_title .= $badge;
		$item_title .= '</span>';

		// Restrict allowed html tags to tags which are considered safe for posts.
		$allowed_tags = wp_kses_allowed_html( 'post' );

		/**return wp_kses($item_title, $allowed_tags);*/
		return $item_title;
	}

	/**
	 * add_menu_item_fields
	 *
	 * Ajout d'un bouton pour ouvrir la popup du formulaire des champs pour le menu
	 */
	public function add_menu_item_fields( $item_id, $item ) {
		// Récupère l'ID de l'article à partir de l'id de l'item du menu
		$post_id = get_post_meta( (int) $item_id, '_menu_item_object_id', true );
		?>
		<p class="eac-field-button description description-thin">
			<label for="menu-item_button-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'EAC Champs', 'eac-components' ); ?><br />
			<button type="button" data-title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" data-id="<?php echo esc_attr( $item_id ); ?>" class="button menu-item_button" name="menu-item_button[<?php echo esc_attr( $item_id ); ?>]" id="menu-item_button-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Afficher les champs', 'eac-components' ); ?></button>
			</label>
		</p>
		<?php
	}

	/**
	 * save_menu_settings
	 *
	 * Sauvegarde les données des champs de la popup pour l'item
	 */
	public function save_menu_settings() {
		$menu_item_id = '';

		$args = array(
			'badge'     => array(
				'content' => '',
				'color'   => '',
				'bgcolor' => '',
			),
			'icon'      => '',
			'thumbnail' => array(
				'state' => '',
				'sizes' => '',
			),
			'image'     => array(
				'url'   => '',
				'sizes' => '',
			),
		);

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), $this->menu_nonce ) ) {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être entegistrés (nonce)", 'eac-components' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'Vous ne pouvez pas modifier les réglages', 'eac-components' ) );
		}

		/**
		 * Les champs 'fields' sont serialisés dans 'eac-nav-menu.js'
		 * Pas de 'sanitize_text_field' de $_POST['fields'] les espaces '%20' sont supprimés
		 * Les champs sont nettoyés individuellement
		 */
		if ( isset( $_POST['fields'] ) ) {
			parse_str( wp_unslash( $_POST['fields'] ), $fields_on );
		} else {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (champs)", 'eac-components' ) );
		}

		// Le post id de l'article du menu
		if ( isset( $fields_on['menu-item_id'] ) && ! empty( $fields_on['menu-item_id'] ) ) {
			$menu_item_id = absint( $fields_on['menu-item_id'] );
		} else {
			wp_send_json_error( esc_html__( "Les réglages n'ont pu être enregistrés (ID)", 'eac-components' ) );
		}

		// Contenu du badge
		if ( isset( $fields_on['menu-item_badge'] ) && ! empty( $fields_on['menu-item_badge'] ) ) {
			$sanitized_data           = sanitize_text_field( $fields_on['menu-item_badge'] );
			$args['badge']['content'] = $sanitized_data;
		}

		// Pick list de la couleur du badge
		if ( isset( $fields_on['menu-item_badge-color-picker'] ) && ! empty( $fields_on['menu-item_badge-color-picker'] ) ) {
			$sanitized_data         = sanitize_text_field( $fields_on['menu-item_badge-color-picker'] );
			$args['badge']['color'] = $sanitized_data;
		}

		// Pick list de la couleur de fond du badge
		if ( isset( $fields_on['menu-item_badge-background-picker'] ) && ! empty( $fields_on['menu-item_badge-background-picker'] ) ) {
			$sanitized_data           = sanitize_text_field( $fields_on['menu-item_badge-background-picker'] );
			$args['badge']['bgcolor'] = $sanitized_data;
		}

		// Pick list des icones
		if ( isset( $fields_on['menu-item_icon-picker'] ) && ! empty( $fields_on['menu-item_icon-picker'] ) ) {
			$sanitized_data = sanitize_text_field( $fields_on['menu-item_icon-picker'] );
			$args['icon']   = $sanitized_data;
		}

		// Miniature du post
		if ( isset( $fields_on['menu-item_thumbnail'] ) ) {
			$args['thumbnail']['state'] = 'checked';
		}

		// Dimension de la miniature
		if ( isset( $fields_on['menu-item_thumbnail-sizes'] ) ) {
			$sanitized_data             = sanitize_text_field( $fields_on['menu-item_thumbnail-sizes'] );
			$args['thumbnail']['sizes'] = $sanitized_data;
		}

		// URL de l'image
		if ( isset( $fields_on['menu-item_image-picker'] ) && ! empty( $fields_on['menu-item_image-picker'] ) ) {
			$sanitized_data       = esc_url_raw( sanitize_text_field( $fields_on['menu-item_image-picker'] ) );
			$args['image']['url'] = $sanitized_data;
		}

		// Dimension de l'image
		if ( isset( $fields_on['menu-item_image-sizes'] ) ) {
			$sanitized_data         = sanitize_text_field( $fields_on['menu-item_image-sizes'] );
			$args['image']['sizes'] = $sanitized_data;
		}

		// Création, mise à jour ou suppression du Meta pour l'item menu ID
		if ( empty( $args['badge']['content'] ) && empty( $args['icon'] ) && empty( $args['thumbnail']['state'] ) && empty( $args['image']['url'] ) ) {
			delete_post_meta( $menu_item_id, $this->meta_item_menu_name );
		} else {
			update_post_meta( $menu_item_id, $this->meta_item_menu_name, $args );
		}

		// retourne 'success' au script JS
		wp_send_json_success( esc_html__( 'Réglages enregistrés', 'eac-components' ) );
	}

} new Eac_Load_Nav_Menu();
