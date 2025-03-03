<?php
/**
 * Class: Eac_Config_Elements
 *
 * Description: Charge les listes de composants et des fonctionnalités
 * Implémente les méthodes pour gérer les éléments
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Eac_Config_Elements class */
class Eac_Config_Elements {

	/**
	 * $manage_options
	 *
	 * @var string
	 */
	private static $manage_options = 'eac_manage_options';

	/**
	 * $options_block_theme
	 *
	 * @var string
	 */
	private static $options_block_theme = 'eac_options_fse';

	/**
	 * L'option de la BDD des filtres WooCommerce
	 *
	 * @var String $options_woo_filter
	 */
	private static $options_woo_hooks = 'eac_options_woo_hooks';

	/**
	 * L'option de la BDD pour les menus de navigation du header footer
	 *
	 * @var String $options_mega_nav_menu
	 */
	private static $options_mega_nav_menu = 'eac_options_nav_menu-';

	/**
	 * L'option de la BDD pour les menus mis en cache
	 *
	 * @var String $options_mega_nav_menu_cache
	 */
	private static $options_mega_nav_menu_cache = 'eac_options_nav_menu_cache';

	/**
	 * L'option de la BDD pour le nombre d'utilisation des éléments par les pages/articles
	 *
	 * @var String $options_usage_count
	 */
	private static $options_usage_count = 'eac_options_usage_count';

	/**
	 * La structure de l'option de la BDD des filtres WooCommerce
	 *
	 * @var Array $woo_shop_args
	 */
	private static $woo_shop_args = array(
		'product-page'   => array(
			'shop'             => array(
				'url' => '',
				'id'  => 0,
			),
			'redirect_buttons' => false,
			'breadcrumb'       => false,
			'metas'            => false,
		),
		'catalog'        => array(
			'active'        => false,
			'request_quote' => false,
		),
		'redirect_pages' => false,
		'mini_cart'      => false,
	);

	/**
	 * $widgets_keys_active
	 *
	 * @var array
	 */
	private static $widgets_keys_active = array();

	/**
	 * $widgets_advanced_keys_active
	 *
	 * @var array
	 */
	private static $widgets_advanced_keys_active = array();

	/**
	 * $widgets_common_keys_active
	 *
	 * @var array
	 */
	private static $widgets_common_keys_active = array();

	/**
	 * $widgets_ehf_keys_active
	 *
	 * @var array
	 */
	private static $widgets_ehf_keys_active = array();

	/**
	 * $features_keys_active
	 *
	 * @var array
	 */
	private static $features_keys_active = array();

	/**
	 * $features_advanced_keys_active
	 *
	 * @var array
	 */
	private static $features_advanced_keys_active = array();

	/**
	 * $features_common_keys_active
	 *
	 * @var array
	 */
	private static $features_common_keys_active = array();

	/**
	 * $elements_list
	 *
	 * @var array
	 */
	private static $elements_list = array();

	/**
	 * $elements_keys_active
	 *
	 * @var array
	 */
	private static $elements_keys_active = array();

	/**
	 * $options_elements_name
	 *
	 * @var string
	 */
	private static $options_elements_name = 'eac_options_elements';

	/**
	 * $element_usage
	 *
	 * La liste des éléments actifs par leur name
	 *
	 * @var array
	 */
	private static $element_usage = array();

	/**
	 * $count_enabled_elements
	 *
	 * @var int
	 */
	private static $count_enabled_elements = 0;

	/**
	 * $count_disabled_elements
	 *
	 * @var int
	 */
	private static $count_disabled_elements = 0;

	/**
	 * $count_all_elements
	 *
	 * @var int
	 */
	private static $count_all_elements = 0;

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'current_screen', array( $this, 'set_element_usage' ) );

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			update_option( self::$options_block_theme, 'true' );
		} else {
			update_option( self::$options_block_theme, 'false' );
		}

		/** Suppression des anciennes options */
		delete_option( 'eac_options_settings' );
		delete_option( 'eac_options_features' );

		/** Charge les tables d'éléments et de fonctionnalités */
		$widgets_list = include __DIR__ . '/eac-config-widgets.php';
		$features_list = include __DIR__ . '/eac-config-features.php';

		/** Construit la liste des éléments actifs */
		self::$elements_list = array_merge( $widgets_list, $features_list );
		foreach ( self::$elements_list as $key => $value ) {
			self::$elements_keys_active[ $key ] = $value['active'];
		}

		/** Enregistre l'option des elements si elle n'existe pas */
		if ( get_option( self::$options_elements_name, false ) ) {
			$this->compare_elements_option();
		} else {
			update_option( self::$options_elements_name, self::$elements_keys_active );
		}

		/** Construit la liste des widgets actives pour leur initialisation */
		foreach ( $widgets_list as $key => $value ) {
			self::$widgets_keys_active[ $key ] = $value['active'];
		}

		/** Construit la liste des fonctionnalités actives pour leur initialisation */
		foreach ( $features_list as $key => $value ) {
			self::$features_keys_active[ $key ] = $value['active'];
		}

		/** Répartir les composants dans les onglets de l'administration */
		$this->distribute_element();

		/** Compte les éléments */
		$this->count_element();
	}

	/**
	 * instance
	 *
	 * @return void
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * distribute_element
	 * Répartir les composants dans leurs groupes d'onglets dans l'admin
	 *
	 * @return void
	 */
	private function distribute_element(): void {
		/** Le nom des composants séparateurs des différents groupes */
		$widgets_advanced_name  = 'all-advanced';
		$widgets_common_name    = 'all-components';
		$widgets_ehf_name       = 'all-ehf';
		$features_advanced_name = 'all-features-advanced';
		$features_common_name   = 'all-features-common';

		/** Position des composants par groupe dans la liste des éléménts actifs */
		$pos_widgets_advanced  = array_search( $widgets_advanced_name, array_keys( self::$elements_keys_active ), true );
		$pos_widgets_common    = array_search( $widgets_common_name, array_keys( self::$elements_keys_active ), true );
		$pos_widgets_ehf       = array_search( $widgets_ehf_name, array_keys( self::$elements_keys_active ), true );
		$pos_features_advanced = array_search( $features_advanced_name, array_keys( self::$elements_keys_active ), true );
		$pos_features_common   = array_search( $features_common_name, array_keys( self::$elements_keys_active ), true );

		self::$widgets_advanced_keys_active  = array_slice( self::$elements_keys_active, $pos_widgets_advanced, $pos_widgets_common );
		self::$widgets_common_keys_active    = array_slice( self::$elements_keys_active, $pos_widgets_common, ( $pos_widgets_ehf - $pos_widgets_common ) );
		self::$widgets_ehf_keys_active       = array_slice( self::$elements_keys_active, $pos_widgets_ehf, ( $pos_features_advanced - $pos_widgets_ehf ) );
		self::$features_advanced_keys_active = array_slice( self::$elements_keys_active, $pos_features_advanced, ( $pos_features_common - $pos_features_advanced ) );
		self::$features_common_keys_active   = array_slice( self::$elements_keys_active, $pos_features_common );
	}

	/**
	 * count_element
	 *
	 * Compte les éléments actifs/inactifs et initialise la table des usges
	 *
	 * @return void
	 */
	private function count_element(): void {
		foreach ( self::$elements_keys_active as $key => $active ) {
			if ( 'all-' !== substr( $key, 0, 4 ) && ( self::are_widget_dependencies_enabled( $key ) || self::are_feature_dependencies_enabled( $key ) ) ) {
				self::$count_all_elements++;
				if ( $active ) {
					$name = self::get_widget_name( $key );
					if ( ! empty( $name ) && ! array_search( $name, self::$element_usage, true ) ) {
						$key = sanitize_text_field( $key );
						self::$element_usage[ $key ] = array(
							'name'  => sanitize_text_field( $name ),
							'count' => 0,
							'perma' => '',
						);
					}
					self::$count_enabled_elements++;
				} else {
					self::$count_disabled_elements++;
				}
			}
		}
	}

	/**
	 * set_element_usage
	 *
	 * Le nombre de articles / pages ou autres post types dans lesquels le widget est utilisé
	 * The number of posts / pages or other post types in which the widget is used
	 *
	 * @return void
	 */
	public function set_element_usage(): void {
		global $wpdb;

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}
		$screen = get_current_screen();

		// Recharge les données d'usage des éléments
		if ( is_object( $screen ) && 'toplevel_page_eac-components' === $screen->id && self::is_feature_active( 'widget-count' ) ) {
			$usage_options = get_option( self::$options_usage_count, false );
			if ( $usage_options ) {
				self::$element_usage = array();
				foreach ( $usage_options as $key => $values ) {
					self::$element_usage[ $key ] = array(
						'name'  => esc_html( $values['name'] ),
						'count' => absint( $values['count'] ),
						'perma' => implode( ',', array_map( 'esc_url', explode( ',', $values['perma'] ) ) ),
					);
				}
			} else {
				foreach ( self::$element_usage as $key => $values ) {
					$post_ids = $wpdb->get_col(
						$wpdb->prepare(
							"SELECT pm.post_id
							FROM {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p
							WHERE pm.post_id = p.ID
							AND pm.meta_key = \"_elementor_data\"
							AND pm.meta_value LIKE %s
							AND p.post_status = \"publish\"
							AND p.post_type <> \"revision\"",
							'%"widgetType":"' . $values['name'] . '"%'
						)
					);
					if ( is_countable( $post_ids ) && ! empty( $post_ids ) ) {
						$perma = array();
						self::$element_usage[ $key ]['count'] = absint( count( $post_ids ) );
						foreach ( $post_ids as $post_id ) {
							$perma_link = get_permalink( $post_id );
							if ( $perma_link ) {
								$perma[] = esc_url_raw( $perma_link );
							}
						}
						self::$element_usage[ $key ]['perma'] = implode( ',', $perma );
					}
				}

				if ( ! empty( self::$element_usage ) ) {
					update_option( self::$options_usage_count, self::$element_usage );
				}
			}
		}
		remove_action( 'current_screen', array( $this, 'set_element_usage' ) );
	}

	/**
	 * get_elements_option_name
	 *
	 * @return String Le libellé de l'option des éléments
	 */
	public static function get_elements_option_name() {
		return self::$options_elements_name;
	}

	/**
	 * get_elements_active
	 *
	 * @return Array La liste des éléments et leur statut
	 */
	public static function get_elements_active() {
		return self::$elements_keys_active;
	}

	/**
	 * compare_elements_option
	 *
	 * Charge les options des éléments
	 * Ajoute/Supprime les éléments en comparant les options par défaut et celles de la BDD
	 */
	private function compare_elements_option() {
		$diff_settings = array();

		/** Récupère les options dans la BDD */
		$bdd_settings = get_option( self::$options_elements_name );

		/** Ajoute les widgets */
		if ( count( self::$elements_keys_active ) > count( $bdd_settings ) ) {
			$diff_settings = array_merge( $bdd_settings, array_diff_key( self::$elements_keys_active, $bdd_settings ) );
			update_option( self::$options_elements_name, $diff_settings );
			/** Supprime les widgets */
		} elseif ( count( self::$elements_keys_active ) < count( $bdd_settings ) ) {
			$diff_settings = array_diff_key( $bdd_settings, array_diff_key( $bdd_settings, self::$elements_keys_active ) );
			update_option( self::$options_elements_name, $diff_settings );
		}

		/** Charge les options mises à jour et les conserve ordonnées avec array_merge */
		self::$elements_keys_active = array_merge( self::$elements_keys_active, get_option( self::$options_elements_name ) );
	}

	/**
	 * get_widgets_active
	 *
	 * @return Array La liste des widgets actives
	 */
	public static function get_widgets_active() {
		return self::$widgets_keys_active;
	}

	/**
	 * get_widgets_advanced_active
	 *
	 * @return Array La liste des widgets avancés actives
	 */
	public static function get_widgets_advanced_active() {
		return self::$widgets_advanced_keys_active;
	}

	/**
	 * get_widgets_common_active
	 *
	 * @return Array La liste des widgets communes actives
	 */
	public static function get_widgets_common_active() {
		return self::$widgets_common_keys_active;
	}

	/**
	 * get_widgets_ehf_active
	 *
	 * @return Array La liste des widgets header/footer actives
	 */
	public static function get_widgets_ehf_active() {
		return self::$widgets_ehf_keys_active;
	}

	/**
	 * is_widget_active
	 *
	 * Découpage de la fonction en deux parties 'active et dépendances'
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool Composant actif
	 */
	public static function is_widget_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			if ( ! self::are_widget_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen de l'élément stocké dans la table des options
			$active = self::$elements_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_widget_dependencies_enabled
	 *
	 * Check si le breadcrumb est actif avec quelques plugin SEO
	 *
	 * @param $element (String) le widget et ses dépendances
	 * @return Bool les dépendances sont actives
	 */
	public static function are_widget_dependencies_enabled( $element ) {

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$elements_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$elements_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}

			/**
			 * Teste si la fonction'yoast_breadcrumb' existe
			 * Teste de l'option Rank Math
			 * Teste si les version PRO Elementor et ACF existent (acf_register_block_type)
			 */
			if ( ! empty( self::$elements_list[ $element ]['elems_exclude'] ) ) {
				foreach ( self::$elements_list[ $element ]['elems_exclude'] as $elem ) {
					if ( 'yoast' === $elem && function_exists( 'yoast_breadcrumb' ) && isset( get_option( 'wpseo_titles' )['breadcrumbs-enable'] ) && get_option( 'wpseo_titles' )['breadcrumbs-enable'] ) {
						return false;
					} elseif ( 'rankmath' === $elem && function_exists( 'rank_math_the_breadcrumbs' ) && isset( get_option( 'rank-math-options-general' )['breadcrumbs'] ) && 'on' === get_option( 'rank-math-options-general' )['breadcrumbs'] ) {
						return false;
					} elseif ( 'seopress' === $elem && function_exists( 'seopress_display_breadcrumbs' ) ) {
						return false;
					} elseif ( 'bcn' === $elem && function_exists( 'bcn_display' ) ) { // NavXT
						return false;
					} elseif ( 'ELEMENTOR_PRO_VERSION' === $elem && defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						return false;
					} elseif ( 'acf_register_block_type' === $elem && function_exists( 'acf_register_block_type' ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * get_widget_path
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le chemin absolu du fichier PHP des composants
	 */
	public static function get_widget_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_path = self::$elements_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_widget_namespace
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le NAMESPACE du composant
	 */
	public static function get_widget_namespace( $element ) {
		$full_class_name = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_class_name = self::$elements_list[ $element ]['name_space'];
			if ( ! empty( $full_class_name ) && class_exists( $full_class_name ) ) {
				return $full_class_name;
			}
		}
		return $full_class_name;
	}

	/**
	 * get_widget_name
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le nom du composant unique
	 */
	public static function get_widget_name( $element ) {
		$name = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && isset( self::$elements_list[ $element ]['name'] ) ) {
			$name = self::$elements_list[ $element ]['name'];
		}
		return $name;
	}

	/**
	 * get_widget_title
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String Le titre du composant traduit dans la locale + le nombre de publications si la fonctionnalité est active
	 */
	public static function get_widget_title( $element ): string {
		$title = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$is_elementor = ( isset( $_POST['action'] ) && 'elementor_element_manager_get_admin_app_data' === $_POST['action'] ) || \Elementor\Plugin::$instance->editor->is_edit_mode();

			if ( 'all-' === substr( $element, 0, 4 ) || empty( self::$element_usage ) || ! self::is_feature_active( 'widget-count' ) || $is_elementor ) {
				$title = sprintf( '%s', esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) );
			} else {
				$count = isset( self::$element_usage[ $element ] ) ? absint( self::$element_usage[ $element ]['count'] ) : '0';
				$perma = isset( self::$element_usage[ $element ] ) ? self::$element_usage[ $element ]['perma'] : '';
				$title = sprintf( '%s %s', '<span class="eac-elements__item-title">' . esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) . '</span>', '<span class="eac-elements__item-count" data-perma="' . $perma . '">' . $count . '</span>' );
			}
		}
		return $title;
	}

	/**
	 * get_widget_icon
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La class Elementor de l'icone du widget
	 */
	public static function get_widget_icon( $element ) {
		$icon = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$icon = self::$elements_list[ $element ]['icon'];
		}
		return $icon;
	}

	/**
	 * get_widget_keywords
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return Array La liste des mots-clés du widget
	 */
	public static function get_widget_keywords( $element ) {
		$keywords = array();

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$keywords = self::$elements_list[ $element ]['keywords'];
		}
		return $keywords;
	}

	/**
	 * get_widget_help_url
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String L'URL de l'aide en ligne du widget
	 */
	public static function get_widget_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help = self::$elements_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_widget_help_url_class
	 *
	 * @param String le composant à checker
	 *
	 * @return String La class de l'aide en ligne du widget
	 */
	public static function get_widget_help_url_class( $element ): string {
		$help_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help_class = self::$elements_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_widget_badge_class
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La class du badge du widget
	 */
	public static function get_widget_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && ! empty( self::$elements_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$elements_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * get_widget_categories
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La catégorie du widget
	 */
	public static function get_widget_categories( $element ) {
		$category = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$category = self::$elements_list[ $element ]['category'];
		}
		return $category;
	}

	/**
	 * get_features_active
	 *
	 * @return Array La liste des fonctionnalités
	 */
	public static function get_features_active() {
		return self::$features_keys_active;
	}

	/**
	 * get_features_advanced_active
	 *
	 * @return Array La liste des fonctionnalités avancées
	 */
	public static function get_features_advanced_active() {
		return self::$features_advanced_keys_active;
	}

	/**
	 * get_features_common_active
	 *
	 * @return Array La liste des fonctionnalités communes
	 */
	public static function get_features_common_active() {
		return self::$features_common_keys_active;
	}

	/**
	 * is_feature_active
	 *
	 * @param $element (String) la fonctionnalité à checker
	 * @return Bool Feature actif
	 */
	public static function is_feature_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			if ( ! self::are_feature_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen du feature stocké dans la table des options
			$active = self::$elements_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_feature_dependencies_enabled
	 *
	 * @param $element (String) le composant et ses dépendances
	 * @return Bool les dépendances sont actives
	 */
	public static function are_feature_dependencies_enabled( $element ) {

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$elements_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$elements_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}

			/** Teste si les version PRO Elementor et ACF existent (acf_register_block_type) */
			if ( ! empty( self::$elements_list[ $element ]['elems_exclude'] ) ) {
				foreach ( self::$elements_list[ $element ]['elems_exclude'] as $elem ) {
					if ( 'ELEMENTOR_PRO_VERSION' === $elem && defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						return false;
					} elseif ( 'acf_register_block_type' === $elem && function_exists( 'acf_register_block_type' ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * get_feature_path
	 *
	 * @param $element (String) la fonctionnnalité à checker
	 * @return String Le chemin absolu du fichier PHP des fonctionnalités
	 */
	public static function get_feature_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_path = self::$elements_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_feature_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre de la fonctionnalité traduite dans la locale
	 */
	public static function get_feature_title( $element ) {
		$title = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$title = sprintf( '%s', esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) );
		}
		return $title;
	}

	/**
	 * get_feature_help_url
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'URL de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help = self::$elements_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_feature_help_url_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url_class( $element ) {
		$help_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help_class = self::$elements_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_feature_badge_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class du badge de la fonctionnalité
	 */
	public static function get_feature_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && ! empty( self::$elements_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$elements_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * get_manage_options_name
	 *
	 * @return le nom de la capacité 'eac_manage_options'
	 */
	public static function get_manage_options_name() {
		return self::$manage_options;
	}

	/**
	 * get_mega_nav_menu_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_mega_nav_menu_option_name() {
		return self::$options_mega_nav_menu;
	}

	/**
	 * get_mega_nav_menu_cache_name
	 *
	 * @return String L'option des menus mis en cache
	 */
	public static function get_mega_nav_menu_cache_name() {
		return self::$options_mega_nav_menu_cache;
	}

	/**
	 * get_woo_hooks_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_name() {
		return self::$options_woo_hooks;
	}

	/**
	 * get_woo_hooks_option_args
	 *
	 * @return Array La structure l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_args() {
		return self::$woo_shop_args;
	}

	/**
	 * Test du type de thème FSE ou non
	 */
	public static function is_a_block_theme() {
		return 'true' === get_option( self::$options_block_theme ) ? true : false;
	}

	/** statistiques des éléments actifs ou non */
	public static function get_count_all_elements() {
		return absint( self::$count_all_elements );
	}

	public static function get_count_enabled_elements() {
		return absint( self::$count_enabled_elements );
	}

	public static function get_count_disabled_elements() {
		return absint( self::$count_disabled_elements );
	}

	/** L'option de sauvegarde de l'usage des éléments */
	public static function get_usage_count_option_name() {
		return self::$options_usage_count;
	}

} Eac_Config_Elements::instance();
