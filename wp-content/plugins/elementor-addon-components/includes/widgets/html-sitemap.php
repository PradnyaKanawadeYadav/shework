<?php
/**
 * Class: Html_Sitemap_Widget
 * Name: HTML Sitemap
 * Slug: eac-addon-html-sitemap
 *
 * Description: Construit et affiche un sitemap au format HTML.
 * 5 types de sitemap: Par Auteurs, Pages, Archives, Taxonomies et Articles qui peuvent être sélectionnés
 * individuellement.
 * Chaque type est entièrement configurable.
 *
 * @since 1.7.1
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\TemplateLibrary\Source_Local;

class Html_Sitemap_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Html_Sitemap_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		EAC_Plugin::instance()->register_script( 'eac-html-sitemap', 'assets/js/elementor/eac-html-sitemap', array( 'jquery', 'elementor-frontend' ), '1.7.1',
			array(
				'strategy' => 'defer',
				'in_footer' => true,
			)
		);
		wp_register_style( 'eac-html-sitemap', EAC_Plugin::instance()->get_style_url( 'assets/css/html-sitemap' ), array( 'eac-frontend' ), '1.7.1' );

		// Filtre la liste 'orderby' utilisée dans les articles et la taxonomie
		add_filter( 'eac/tools/post_orderby',
			function( $exclude_orderby ) {
				$exclude = array(
					'ID'             => 'ID',
					'author'         => 'author',
					'comment_count'  => 'comment_count',
					'meta_value_num' => 'meta_value_num',
				);
				return array_diff_key( $exclude_orderby, $exclude );
			},
			10
		);
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'html-sitemap';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return widget icon.
	 */
	public function get_icon() {
		return Eac_Config_Elements::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return widget category.
	 */
	public function get_categories() {
		return Eac_Config_Elements::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries list.
	 */
	public function get_script_depends(): array {
		return array( 'eac-html-sitemap' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-html-sitemap' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
		return Eac_Config_Elements::get_widget_help_url( $this->slug );
	}

	/**
	 * has_widget_inner_wrapper
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		/** Generale Content Section */
		$this->start_controls_section(
			'stm_content_sitemap',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'stm_content_display_author',
				array(
					'label'        => esc_html__( 'Sitemap Auteur', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_content_display_page',
				array(
					'label'        => esc_html__( 'Sitemap Page', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_content_display_archive',
				array(
					'label'        => esc_html__( 'Sitemap Archive', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_content_display_taxonomy',
				array(
					'label'        => esc_html__( 'Sitemap Taxonomie', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_content_display_post',
				array(
					'label'        => esc_html__( 'Sitemap Article', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_global_setting',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/** Titre */
			$this->add_control(
				'stm_title_settings',
				array(
					'label'     => esc_html__( 'Titre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'stm_global_title_tag',
				array(
					'label'   => esc_html__( 'Étiquette du titre', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h2',
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

			/** Layout */
			$this->add_control(
				'stm_layout_settings',
				array(
					'label'     => esc_html__( 'Disposition', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'stm_setting_column',
				array(
					'label'        => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
					),
					'prefix_class' => 'responsive%s-',
				)
			);

			/** Marge */
			$this->add_control(
				'stm_content_settings',
				array(
					'label'     => esc_html__( 'Contenu', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'stm_global_margin_left',
				array(
					'label'          => esc_html__( 'Marge', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( 'px', 'em' ),
					'default'        => array(
						'size' => 2,
						'unit' => 'em',
					),
					'tablet_default' => array(
						'unit' => 'em',
					),
					'mobile_default' => array(
						'unit' => 'em',
					),
					'range'          => array(
						'px' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 1,
						),
						'em' => array(
							'min'  => 0,
							'max'  => 10,
							'step' => 0.1,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list' => 'margin-inline-start: {{SIZE}}{{UNIT}};',
					),
				)
			);

			/** Pictogrammes */
			$this->add_control(
				'stm_content_picto',
				array(
					'label'     => esc_html__( 'Pictogrammes', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_content_picto_before',
				array(
					'label'                  => esc_html__( 'Pictogramme premier niveau', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'skin'                   => 'inline',
					'default'                => array(
						'value'   => 'fas fa-arrow-right',
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_control(
				'stm_content_picto_after',
				array(
					'label'                  => esc_html__( 'Pictogramme autres niveaux', 'eac-components' ),
					'type'                   => Controls_Manager::ICONS,
					'skin'                   => 'inline',
					'default'                => array(
						'value'   => 'fas fa-arrow-right',
						'library' => 'fa-solid',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_author_setting',
			array(
				'label'     => esc_html__( 'Réglage Auteur', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'stm_content_display_author' => 'yes' ),
			)
		);

			$this->add_control(
				'stm_author_titre',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Auteurs', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'stm_author_post_count',
				array(
					'label'        => esc_html__( "Afficher le nombre d'articles", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_author_post_fullname',
				array(
					'label'        => esc_html__( 'Afficher le nom complet', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_author_exclude',
				array(
					'label'       => esc_html__( 'Exclure des auteurs', 'eac-components' ),
					'description' => esc_html__( "Balises dynamiques 'Article/Auteurs'", 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_page_setting',
			array(
				'label'     => esc_html__( 'Réglage Page', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'stm_content_display_page' => 'yes' ),
			)
		);

			$this->add_control(
				'stm_page_titre',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Pages', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'stm_page_exclude',
				array(
					'label'       => esc_html__( 'Exclure des pages', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => Eac_Tools_Util::get_pages_by_id(),
					'multiple'    => true,
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_archive_setting',
			array(
				'label'     => esc_html__( 'Réglage Archive', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'stm_content_display_archive' => 'yes' ),
			)
		);

			$this->add_control(
				'stm_archive_titre',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Archives', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'stm_archive_post_count',
				array(
					'label'        => esc_html__( "Afficher le nombre d'articles", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'stm_archive_count',
				array(
					'label'       => esc_html__( "Nombre d'entrées", 'eac-components' ),
					'description' => esc_html__( '0 = toutes les archives', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'default'     => 0,
				)
			);

			$this->add_control(
				'stm_archive_frequence',
				array(
					'label'   => esc_html__( 'Publication', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'daily'   => esc_html__( 'Journalière', 'eac-components' ),
						'weekly'  => esc_html__( 'Hebdomadaire', 'eac-components' ),
						'monthly' => esc_html__( 'Mensuelle', 'eac-components' ),
						'yearly'  => esc_html__( 'Annuelle', 'eac-components' ),
					),
					'default' => 'monthly',

				)
			);

			$this->add_control(
				'stm_archive_type',
				array(
					'label'       => esc_html__( "Type d'article", 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => Eac_Tools_Util::get_all_post_types(),
					'default'     => 'post',
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_taxonomy_setting',
			array(
				'label'     => esc_html__( 'Réglage Taxonomie', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'stm_content_display_taxonomy' => 'yes' ),
			)
		);

			$this->add_control(
				'stm_taxonomy_titre',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Taxonomies', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'stm_taxonomy_count',
				array(
					'label'        => esc_html__( "Afficher le nombre d'articles", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_comment',
				array(
					'label'        => esc_html__( 'Afficher le nombre de commentaires', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_date',
				array(
					'label'        => esc_html__( 'Afficher la date', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_nofollow',
				array(
					'label'        => esc_html__( "Ajouter 'nofollow' aux liens", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_exclude',
				array(
					'label'       => esc_html__( 'Exclure des taxonomies', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => Eac_Tools_Util::get_all_taxonomies(),
					'default'     => array( 'post_tag' ),
					'multiple'    => true,
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'stm_taxonomy_id',
				array(
					'label'        => esc_html__( 'Afficher les IDs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_exclude_id',
				array(
					'label'       => esc_html__( 'Exclure IDs', 'eac-components' ),
					'description' => esc_html__( 'Les ID séparés par une virgule sans espace', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '',
				)
			);

			$this->add_control(
				'stm_taxonomy_orderby',
				array(
					'label'     => esc_html__( 'Triés par', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => Eac_Tools_Util::get_post_orderby(),
					'default'   => 'title',
				)
			);

			$this->add_control(
				'stm_taxonomy_order',
				array(
					'label'   => esc_html__( 'Affichage', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'asc'  => esc_html__( 'Ascendant', 'eac-components' ),
						'desc' => esc_html__( 'Descendant', 'eac-components' ),
					),
					'default' => 'asc',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'stm_post_setting',
			array(
				'label'     => esc_html__( 'Réglage Article', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'stm_content_display_post' => 'yes' ),
			)
		);

			$this->add_control(
				'stm_post_titre',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Articles', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'stm_post_title_tag',
				array(
					'label'   => esc_html__( 'Étiquette des titres', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'h3',
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
				)
			);

			$this->add_control(
				'stm_post_comment',
				array(
					'label'        => esc_html__( 'Afficher le nombre de commentaires', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_post_category',
				array(
					'label'        => esc_html__( 'Afficher les catégories', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_post_date',
				array(
					'label'        => esc_html__( 'Afficher la date', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_post_nofollow',
				array(
					'label'        => esc_html__( "Ajouter 'nofollow' aux liens", 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_post_type_exclude',
				array(
					'label'       => esc_html__( "Exclure des types d'articles", 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => Eac_Tools_Util::get_all_post_types(),
					'default'     => array( 'page', 'attachment', Source_Local::CPT, 'ae_global_templates', 'sdm_downloads', 'eac_options_page' ),
					'multiple'    => true,
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'stm_post_id',
				array(
					'label'        => esc_html__( 'Afficher les IDs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'stm_post_exclude',
				array(
					'label'       => esc_html__( 'Exclure IDs', 'eac-components' ),
					'description' => esc_html__( 'Les ID séparés par une virgule sans espace', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '',
				)
			);

			$this->add_control(
				'stm_post_orderby',
				array(
					'label'     => esc_html__( 'Triés par', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => Eac_Tools_Util::get_post_orderby(),
					'default'   => 'title',
				)
			);

			$this->add_control(
				'stm_post_order',
				array(
					'label'   => esc_html__( 'Affichage', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'asc'  => esc_html__( 'Ascendant', 'eac-components' ),
						'desc' => esc_html__( 'Descendant', 'eac-components' ),
					),
					'default' => 'asc',
				)
			);

		$this->end_controls_section();

		/** Generale Style Section */
		$this->start_controls_section(
			'stm_general_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** Conteneur */
			$this->add_control(
				'stm_container_style',
				array(
					'label'     => esc_html__( 'Conteneur', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'stm_global_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-1',
					'options'      => array(
						'style-0'  => esc_html__( 'Défaut', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-6'  => 'Style 6',
						'style-7'  => 'Style 7',
						'style-8'  => 'Style 8',
						'style-9'  => 'Style 9',
					),
					'prefix_class' => 'html-sitemap__wrapper-',
				)
			);

			$this->add_control(
				'stm_global_height',
				array(
					'label'        => esc_html__( 'Colonnes de même hauteur', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'no',
					'toggle'       => false,
					'prefix_class' => 'responsive-height-',
				)
			);

			$this->add_control(
				'stm_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} article>div[class*="sitemap-"]' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'stm_global_border',
					'selector'  => '{{WRAPPER}} article>div[class*="sitemap-"]',
					'condition' => array( 'stm_global_style' => 'style-0' ),
				)
			);

			$this->add_control(
				'stm_global_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} article>div[class*="sitemap-"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'stm_global_style' => 'style-0' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'stm_global_shadow',
					'label'     => esc_html__( 'Ombre portée', 'eac-components' ),
					'selector'  => '{{WRAPPER}} article>div[class*="sitemap-"]',
					'condition' => array( 'stm_global_style' => 'style-0' ),
				)
			);

			/** Titre */
			$this->add_control(
				'stm_title_style',
				array(
					'label'     => esc_html__( 'Titre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'stm_title_alignment',
				array(
					'label'                => esc_html__( 'Alignement', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'default'              => 'left',
					'options'              => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-text-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-text-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => '0 auto',
						'center' => 'auto',
						'right'  => 'auto 0',
					),
					'selectors'            => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title' => 'margin-inline: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'stm_title_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'stm_title_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title',
				)
			);

			$this->add_control(
				'stm_post_title_color',
				array(
					'label'     => esc_html__( 'Couleur sous-titre', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list .posts-post-title' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'stm_post_title_typography',
					'label'    => esc_html__( 'Typographie sous-titre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list .posts-post-title',
				)
			);

			/** Texte */
			$this->add_control(
				'stm_texte_style',
				array(
					'label'     => esc_html__( 'Texte', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'stm_texte_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list ul li a',
				)
			);

			/** Auteur */
			$this->add_control(
				'stm_author_style',
				array(
					'label'     => esc_html__( 'Auteur', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'stm_content_display_author' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_author_picto_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-authors .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-authors .sitemap-posts-list ul li a,
						{{WRAPPER}} .eac-html-sitemap .sitemap-authors .sitemap-posts-list ul li span' => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( 'stm_content_display_author' => 'yes' ),
				)
			);

			$this->add_control(
				'stm_author_background_color',
				array(
					'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#90EE90',
					'selectors' => array( '{{WRAPPER}} .eac-html-sitemap .sitemap-authors' => 'color: {VALUE}};' ),
					'condition' => array(
						'stm_content_display_author' => 'yes',
						'stm_global_style!' => 'style-0',
					),
				)
			);

			/** Page */
			$this->add_control(
				'stm_page_style',
				array(
					'label'     => esc_html__( 'Page', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'stm_content_display_page' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_page_picto_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-pages .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-pages .sitemap-posts-list ul li a,
						{{WRAPPER}} .eac-html-sitemap .sitemap-pages .sitemap-posts-list ul li span' => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( 'stm_content_display_page' => 'yes' ),
				)
			);

			$this->add_control(
				'stm_page_background_color',
				array(
					'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFC077',
					'selectors' => array( '{{WRAPPER}} .eac-html-sitemap .sitemap-pages' => 'color: {{VALUE}};' ),
					'condition' => array(
						'stm_content_display_page' => 'yes',
						'stm_global_style!' => 'style-0',
					),
				)
			);

			/** Archive */
			$this->add_control(
				'stm_archive_style',
				array(
					'label'     => esc_html__( 'Archive', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'stm_content_display_archive' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_archive_picto_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-archives .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-archives .sitemap-posts-list ul li a,
						{{WRAPPER}} .eac-html-sitemap .sitemap-archives .sitemap-posts-list ul li span' => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( 'stm_content_display_archive' => 'yes' ),
				)
			);

			$this->add_control(
				'stm_archive_background_color',
				array(
					'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFFF00',
					'selectors' => array( '{{WRAPPER}} .eac-html-sitemap .sitemap-archives' => 'color: {{VALUE}};' ),
					'condition' => array(
						'stm_content_display_archive' => 'yes',
						'stm_global_style!' => 'style-0',
					),
				)
			);

			/** Taxonomie */
			$this->add_control(
				'stm_taxonomy_style',
				array(
					'label'     => esc_html__( 'Taxonomie', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'stm_content_display_taxonomy' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_taxonomy_picto_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies .sitemap-posts-list ul li a,
						{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies .sitemap-posts-list ul li span' => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( 'stm_content_display_taxonomy' => 'yes' ),
				)
			);

			$this->add_control(
				'stm_taxonomy_background_color',
				array(
					'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#A299FF',
					'selectors' => array( '{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies' => 'color: {{VALUE}};' ),
					'condition' => array(
						'stm_content_display_taxonomy' => 'yes',
						'stm_global_style!' => 'style-0',
					),
				)
			);

			/** Article */
			$this->add_control(
				'stm_post_style',
				array(
					'label'     => esc_html__( 'Article', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'stm_content_display_post' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'stm_post_picto_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts .sitemap-posts-list ul li,
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts .sitemap-posts-list ul li a,
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts .sitemap-posts-list ul li span' => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( 'stm_content_display_post' => 'yes' ),
				)
			);

			$this->add_control(
				'stm_post_background_color',
				array(
					'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#00FF00',
					'selectors' => array( '{{WRAPPER}} .eac-html-sitemap .sitemap-posts' => 'color: {{VALUE}};' ),
					'condition' => array(
						'stm_content_display_post' => 'yes',
						'stm_global_style!' => 'style-0',
					),
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		global $post;
		?>
		<div class="eac-html-sitemap">
			<article id="post-<?php echo esc_attr( $post->ID ); ?>" <?php post_class( 'site-map-article' ); ?>>
				<?php $this->render_sitemap(); ?>
			</article>
			<div class='eac-skip-grid' tabindex='0'>
				<span class='visually-hidden'><?php esc_html_e( 'Sortir de la grille', 'eac-components' ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * render_sitemap
	 *
	 * Affiche le contenu du sitemap selon configuration
	 *
	 * @access protected
	 */
	protected function render_sitemap() {
		$settings  = $this->get_settings_for_display();
		$title_tag = ! empty( $settings['stm_global_title_tag'] ) ? Utils::validate_html_tag( $settings['stm_global_title_tag'] ) : 'div';

		if ( 'yes' === $settings['stm_content_display_author'] ) {
			$this->eac_get_html_sitemap_authors( $title_tag );
		}
		if ( 'yes' === $settings['stm_content_display_page'] ) {
			$this->eac_get_html_sitemap_pages( $title_tag );
		}
		if ( 'yes' === $settings['stm_content_display_archive'] ) {
			$this->eac_get_html_sitemap_archives( $title_tag );
		}
		if ( 'yes' === $settings['stm_content_display_taxonomy'] ) {
			$this->eac_get_html_sitemap_taxonomies( $title_tag );
		}
		if ( 'yes' === $settings['stm_content_display_post'] ) {
			$this->eac_get_html_sitemap_posts( $title_tag );
		}
	}

	/**
	 * eac_get_html_sitemap_authors
	 *
	 * Description: affiche la liste des articles des auteurs
	 */
	protected function eac_get_html_sitemap_authors( $title_tag ) {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'stm_author_titre', 'none' );
		$this->add_render_attribute( 'stm_author_titre', 'class', 'sitemap-posts-title sitemap-title-top' );
		$this->add_render_attribute( 'stm_author_titre', 'aria-label', esc_html__( 'Sitemap auteurs', 'eac-components' ) );
		$this->add_render_attribute( 'stm_author_titre', 'tabindex', '0' );
		?>
		<div class='sitemap-authors'>
			<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'stm_author_titre' ); ?>>
				<?php echo esc_html( $settings['stm_author_titre'] ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class='sitemap-posts-list eac-icon-svg'>
				<?php
				$exclude     = $settings['stm_author_exclude'];
				$optioncount = 'yes' === $settings['stm_author_post_count'] ? true : false;
				$fullname    = 'yes' === $settings['stm_author_post_fullname'] ? true : false;
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_before'], array( 'aria-hidden' => 'true' ) );
				$icon = ob_get_clean();
				ob_start(); ?>
				<ul>
					<?php
					$list_authors = wp_list_authors(
						array(
							'orderby'       => 'post_count',
							'order'         => 'DESC',
							'exclude'       => $exclude,
							'optioncount'   => $optioncount,
							'show_fullname' => $fullname,
							'echo'          => false,
							'style'         => '',
						)
					);
					foreach ( explode( ',', $list_authors ) as $list_author ) {
						echo wp_kses_post( '<li>' . $icon . $list_author . '</li>' );
					} ?>
				</ul>
				<?php echo ob_get_clean();// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * eac_get_html_sitemap_pages
	 *
	 * Description: affiche la liste des pages
	 */
	protected function eac_get_html_sitemap_pages( $title_tag ) {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'stm_page_titre', 'none' );
		$this->add_render_attribute( 'stm_page_titre', 'class', 'sitemap-posts-title sitemap-title-top' );
		$this->add_render_attribute( 'stm_page_titre', 'aria-label', esc_html__( 'Sitemap pages', 'eac-components' ) );
		$this->add_render_attribute( 'stm_page_titre', 'tabindex', '0' );
		?>
		<div class='sitemap-pages'>
			<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'stm_page_titre' ); ?>>
				<?php echo esc_html( $settings['stm_page_titre'] ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class='sitemap-posts-list eac-icon-svg'>
				<?php
				// Exclusion de page par leur ID
				$exclude = ! empty( $settings['stm_page_exclude'] ) ? implode( ',', $settings['stm_page_exclude'] ) : '';
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_before'], array( 'aria-hidden' => 'true' ) );
				$icon = ob_get_clean();
				ob_start(); ?>
				<ul>
					<?php
					wp_list_pages(
						array(
							'exclude'     => $exclude,
							'title_li'    => '',
							'link_before' => $icon,
						)
					); ?>
				</ul>
				<?php echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * eac_get_html_sitemap_archives
	 *
	 * Description: affiche la liste des archives par types d'articles
	 */
	protected function eac_get_html_sitemap_archives( $title_tag ) {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'stm_archive_titre', 'none' );
		$this->add_render_attribute( 'stm_archive_titre', 'class', 'sitemap-posts-title sitemap-title-top' );
		$this->add_render_attribute( 'stm_archive_titre', 'aria-label', esc_html__( 'Sitemap archives', 'eac-components' ) );
		$this->add_render_attribute( 'stm_archive_titre', 'tabindex', '0' );
		?>
		<div class='sitemap-archives'>
			<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'stm_archive_titre' ); ?>>
				<?php echo esc_html( $settings['stm_archive_titre'] ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class='sitemap-posts-list eac-icon-svg'>
				<?php
				$post_types = $settings['stm_archive_type'];
				$type       = $settings['stm_archive_frequence'];
				$showcount  = 'yes' === $settings['stm_archive_post_count'] ? true : false;
				$limit      = 0 === $settings['stm_archive_count'] ? 100 : $settings['stm_archive_count'];
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_before'], array( 'aria-hidden' => 'true' ) );
				$icon = ob_get_clean();
				ob_start() ?>
				<ul>
					<?php
					$archives = wp_get_archives(
						array(
							'post_type'       => $post_types,
							'type'            => $type,
							'show_post_count' => $showcount,
							'before'          => $icon,
							'limit'           => absint( $limit ),
						)
					); ?>
				</ul>
				<?php echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * eac_get_html_sitemap_taxonomies
	 *
	 * Description:  affiche la liste des pages par leurs taxonomies
	 */
	protected function eac_get_html_sitemap_taxonomies( $title_tag ) {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'stm_taxonomy_titre', 'none' );
		$this->add_render_attribute( 'stm_taxonomy_titre', 'class', 'sitemap-posts-title sitemap-title-top' );
		$this->add_render_attribute( 'stm_taxonomy_titre', 'aria-label', esc_html__( 'Sitemap taxonomies', 'eac-components' ) );
		$this->add_render_attribute( 'stm_taxonomy_titre', 'tabindex', '0' );
		?>
		<div class='sitemap-taxonomies'>
			<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'stm_taxonomy_titre' ); ?>>
				<?php echo esc_html( $settings['stm_taxonomy_titre'] ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class='sitemap-posts-list eac-icon-svg'>
				<?php
				global $post;

				// Toutes les taxonomies déjà filtrées
				$all_taxos = Eac_Tools_Util::get_all_taxonomies();

				// Les taxonomies exclues
				$exclude_taxos = $settings['stm_taxonomy_exclude'];

				// Trie
				$orderby = $settings['stm_taxonomy_orderby'];

				// Descendant/Ascendant
				$order = $settings['stm_taxonomy_order'];

				// Afficher la date
				$has_date = 'yes' === $settings['stm_taxonomy_date'] ? true : false;
				$la_date  = '';

				// Affiche le nombre de catégories
				$has_cat_count = 'yes' === $settings['stm_taxonomy_count'] ? true : false;

				// Affiche le nombre de commentaires
				$has_comment = 'yes' === $settings['stm_taxonomy_comment'] ? true : false;
				$c_count     = '';

				// Ajout de nofollow aux liens
				$has_nofollow = 'yes' === $settings['stm_taxonomy_nofollow'] ? 'rel="nofollow"' : '';

				// Exclusion d'articles
				$has_id       = 'yes' === $settings['stm_taxonomy_id'] ? true : false;
				$exclude_post = ! empty( $settings['stm_taxonomy_exclude_id'] ) ? explode( ',', sanitize_text_field( $settings['stm_taxonomy_exclude_id'] ) ) : '';

				// Les pictogrammes
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_before'], array( 'aria-hidden' => 'true' ) );
				$icon_before = ob_get_clean();
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_after'], array( 'aria-hidden' => 'true' ) );
				$icon_after = ob_get_clean();

				// Boucle sur les taxonomies
				ob_start(); ?>
				<ul>
				<?php foreach ( $all_taxos as $taxo => $value ) {
					if ( empty( $exclude_taxos ) || ( ! empty( $exclude_taxos ) && ! in_array( $taxo, $exclude_taxos, true ) ) ) {
						// Les catégories de la taxonomie
						$categories = get_categories(
							array(
								'taxonomy'   => $taxo,
								'hide_empty' => true,
							)
						);

						// Boucle sur chaque catégorie de la taxonomie
						foreach ( $categories as $categorie ) {
							// Le type de post de la catégorie
							$post_object = get_taxonomy( $categorie->taxonomy );
							if ( ! $post_object ) {
								continue;
							}

							// Le nombre d'occurrence de la catégorie
							$cat_count = '';
							if ( $has_cat_count ) {
								$cat_count = ' (' . $categorie->category_count . ')';
							}

							// Arguments de la requête
							$args = array(
								'post_type'      => $post_object->object_type,
								'posts_per_page' => -1,
								'orderby'        => $orderby,
								'order'          => $order,
								'post__not_in'   => $exclude_post,
								'tax_query'      => array(
									array(
										'taxonomy' => $taxo,
										'field'    => 'id',
										'terms'    => $categorie->cat_ID,
									),
								),
							);

							// Les articles
							$posts_array = get_posts( $args );

							// Il y a des posts
							if ( ! is_wp_error( $posts_array ) && ! empty( $posts_array ) ) {
								// Affiche le nom de la taxonomie
								?>
								<li class='taxo-item'>
									<span><b><?php echo esc_attr( ucfirst( $taxo ) ) . wp_kses_post( $icon_before ); ?></b></span>
									<span><a class='eac-accessible-link' href="<?php echo esc_url( get_category_link( $categorie->cat_ID ) ); ?>" <?php echo esc_attr( $has_nofollow ); ?> aria-label="<?php echo esc_html__( 'Voir la taxonomie', 'eac-components' ) . ' ' . esc_html( $categorie->cat_name ); ?>"><?php echo esc_html( $categorie->cat_name ); ?></a></span>
									<span><?php echo esc_attr( $cat_count ); ?></span>
									<ul>
									<?php

									foreach ( $posts_array as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
										// Renseigne les variables globales de l'article courant
										setup_postdata( $post );

										// L'ID de l'article
										$id = '';
										if ( $has_id ) {
											$id = ' (' . get_the_ID() . ')';
										}

										// Affiche la date
										if ( $has_date ) {
											if ( 'modified' === $orderby ) {
												$la_date = $icon_after . date_i18n( get_option( 'date_format' ), strtotime( get_the_modified_date( 'Y-m-d' ) ) );
											} else {
												$la_date = $icon_after . date_i18n( get_option( 'date_format' ), strtotime( get_the_date( 'Y-m-d' ) ) );
											}
										}
										// Affiche le nombre de commentaires
										if ( $has_comment ) {
											$c_count = ' (' . get_comments_number() . ')';
										}
										?>
										<li class='post-item'>
											<span><?php echo wp_kses_post( $icon_after ); ?></span>
											<span>
												<a class='eac-accessible-link' href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" <?php echo esc_html( $has_nofollow ); ?> aria-label="<?php echo esc_html__( "Voir l'article", 'eac-components' ) . ' ' . esc_html( get_the_title( get_the_ID() ) ); ?>"><?php the_title(); ?></a>
											</span>
											<?php if ( $has_comment ) { ?>
												<span><?php echo esc_attr( $c_count ); ?></span>
											<?php }
											if ( $has_id ) { ?>
												<span><?php echo esc_attr( $id ); ?></span>
											<?php }
											if ( $has_date ) { ?>
												<span><?php echo wp_kses_post( $la_date ); ?></span>
											<?php } ?>
										</li>
										<?php
									}
									wp_reset_postdata();
									?>
									</ul> 
								</li>
								<?php
							}
						}       // End foreach categories
					}           // End If taxonomie exclue
				}               // End foreach taxonomie
				?>
				</ul>
				<?php echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * eac_get_html_sitemap_posts
	 *
	 * Description: affiche la liste des articles
	 */
	protected function eac_get_html_sitemap_posts( $title_tag ) {
		$settings = $this->get_settings_for_display();
		$this->add_inline_editing_attributes( 'stm_post_titre', 'none' );
		$this->add_render_attribute( 'stm_post_titre', 'class', 'sitemap-posts-title sitemap-title-top' );
		$this->add_render_attribute( 'stm_post_titre', 'aria-label', esc_html__( 'Sitemap articles', 'eac-components' ) );
		$this->add_render_attribute( 'stm_post_titre', 'tabindex', '0' );
		?>
		<div class="sitemap-posts">
			<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'stm_post_titre' ); ?>>
				<?php echo esc_html( $settings['stm_post_titre'] ); ?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class='sitemap-posts-list eac-icon-svg'>
				<?php
				global $post;

				// Trie
				$orderby = $settings['stm_post_orderby'];

				// Descendant/Ascendant
				$order = $settings['stm_post_order'];

				// Afficher la date
				$has_date = 'yes' === $settings['stm_post_date'] ? true : false;
				$la_date  = '';

				// Affiche le nombre de commentaires
				$has_comment = 'yes' === $settings['stm_post_comment'] ? true : false;
				$c_count     = '';

				// Ajout de nofollow aux liens
				$has_nofollow = 'yes' === $settings['stm_post_nofollow'] ? 'rel="nofollow"' : '';

				// Les catégories par article
				$has_category = 'yes' === $settings['stm_post_category'] ? true : false;
				$categories   = '';

				// Exclusion d'articles
				$has_id       = 'yes' === $settings['stm_post_id'] ? true : false;
				$exclude_post = ! empty( $settings['stm_post_exclude'] ) ? explode( ',', sanitize_text_field( $settings['stm_post_exclude'] ) ) : '';

				// Exclure des types d'articles
				$exclude_posttype = array();
				$exclude_posttype = $settings['stm_post_type_exclude'];

				// Les pictogrammes
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_before'], array( 'aria-hidden' => 'true' ) );
				$icon_before = ob_get_clean();
				ob_start();
				Icons_Manager::render_icon( $settings['stm_content_picto_after'], array( 'aria-hidden' => 'true' ) );
				$icon_after = ob_get_clean();

				// Filtre la taxonomie
				add_filter(
					'eac/tools/taxonomies_by_name',
					function( $filter_taxo ) {
						$exclude = array( 'post_tag' );
						return array_merge( $filter_taxo, $exclude );
						/**
						 * $include = [''];
						 * return array_diff($filter_taxo, $include);
						 * $exclude_key = ['ID' => 'ID', 'author' => 'author', 'comment_count' => 'comment_count', 'meta_value_num' => 'meta_value_num'];
						 * return array_diff_key($exclude_orderby, $exclude_key);
						 */
					},
					10
				);

				// Récupère toute la taxonomie filtrée par le nom
				$all_taxo = Eac_Tools_Util::get_all_taxonomies_by_name();

				// Récupère tous les types d'articles
				$post_types = get_post_types( array( 'public' => true ), 'objects' );

				// Boucle sur les types d'articles
				ob_start();
				foreach ( $post_types as $post_type ) {
					if ( empty( $exclude_posttype ) || ( ! empty( $exclude_posttype ) && ! in_array( $post_type->name, $exclude_posttype, true ) ) ) {
						?>
						<<?php echo esc_attr( $title_tag ); ?> class='posts-post-title'><?php echo esc_html( $post_type->labels->name ); ?></<?php echo esc_attr( $title_tag ); ?>>
						<?php
						$args = array(
							'posts_per_page' => -1,
							'post_type'      => $post_type->name,
							'post_status'    => 'publish',
							'orderby'        => $orderby,
							'order'          => $order,
							'post__not_in'   => $exclude_post,
						);

						// Les articles
						$posts_array = get_posts( $args );
						?>
						<ul>
						<?php if ( ! is_wp_error( $posts_array ) && ! empty( $posts_array ) ) {
							foreach ( $posts_array as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								setup_postdata( $post ); // Renseigne les variables globales de l'article courant

								// L'ID de l'article
								$id = '';
								if ( $has_id ) {
									$id = ' (' . get_the_ID() . ')';
								}

								// Affiche et formate la date
								if ( $has_date ) {
									if ( 'modified' === $orderby ) {
										$la_date = $icon_after . date_i18n( get_option( 'date_format' ), strtotime( get_the_modified_date( 'Y-m-d' ) ) );
									} else {
										$la_date = $icon_after . date_i18n( get_option( 'date_format' ), strtotime( get_the_date( 'Y-m-d' ) ) );
									}
								}

								// Affiche le nombre de commentaires
								if ( $has_comment ) {
									$c_count = ' (' . get_comments_number() . ')';
								}

								/** Les catégories sont renseignées */
								$display_categories = '';
								if ( $has_category ) {
									// Récupère toutes les catégories de l'article
									$post_categories = wp_get_post_terms( get_the_ID(), $all_taxo );
									// Recherche les catégories de l'article
									if ( ! is_wp_error( $post_categories ) && ! empty( $post_categories ) ) {
										$categories = array();
										foreach ( $post_categories as $category ) {
											$term_link = get_term_link( (int) $category->term_id );
											if ( ! is_wp_error( $term_link ) && ! empty( $term_link ) ) {
												$categories[] = '<a class="eac-accessible-link" href="' . esc_url( $term_link ) . '" aria-label="' . esc_html__( 'Voir la taxonomie', 'eac-components' ) . ' ' . esc_html( $category->name ) . '">' . esc_html( $category->name ) . '</a>';
											}
										}
										if ( ! empty( $categories ) ) {
											$display_categories = $icon_after . implode( ', ', $categories );
										}
									}
								}
								?>
								<li class='post-item'>
									<span><?php echo wp_kses_post( $icon_before ); ?></span>
									<span>
										<a class='eac-accessible-link' href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" <?php echo esc_html( $has_nofollow ); ?> aria-label="<?php echo esc_html__( "Voir l'article", 'eac-components' ) . ' ' . esc_html( get_the_title( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title( get_the_ID() ) ); ?></a>
									</span>
									<?php if ( $has_comment ) { ?>
										<span><?php echo esc_attr( $c_count ); ?></span>
									<?php }
									if ( $has_id ) { ?>
										<span><?php echo esc_attr( $id ); ?></span>
									<?php }
									if ( $has_category ) { ?>
										<span><?php echo wp_kses_post( $display_categories ); ?></span>
									<?php }
									if ( $has_date ) { ?>
										<span><?php echo wp_kses_post( $la_date ); ?></span>
									<?php } ?>
								</li>
								<?php
							}
						}
						wp_reset_postdata(); ?>
						</ul>
						<?php
					}
				}
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
