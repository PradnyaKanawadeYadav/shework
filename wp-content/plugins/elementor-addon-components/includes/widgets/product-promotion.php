<?php
/**
 * Class: Product_Promotion_Widget
 * Name: Promotion de produit
 * Slug: eac-addon-image-promo
 *
 * Description: affiche et met en forme la promotion d'un produit
 *
 * @since 1.0.0
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
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;

class Product_Promotion_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Product_Promotion_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-product-promotion', EAC_Plugin::instance()->get_style_url( 'assets/css/product-promotion' ), array( 'eac-frontend' ), '1.0.0' );
		wp_register_style( 'eac-image-ribbon', EAC_Plugin::instance()->get_style_url( 'assets/css/image-ribbon' ), array( 'eac-product-promotion' ), '1.0.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'image-promotion';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return string widget icon.
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
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-product-promotion', 'eac-image-ribbon' );
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
		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'ip_image_settings',
			array(
				'label' => esc_html__( 'Image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			// Ajout de l'image
			$this->add_control(
				'ip_image_switcher',
				array(
					'label'        => esc_html__( 'Ajouter une image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ip_icon_switcher',
				array(
					'label'        => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'ip_image_switcher!' => 'yes' ),
				)
			);

			/** 1.7.80 Utilisation du control ICONS */
			$this->add_control(
				'ip_icon_content_new',
				array(
					'label'            => esc_html__( 'Choix du pictogramme', 'eac-components' ),
					'type'             => Controls_Manager::ICONS,
					'default'          => array(
						'value'   => 'fas fa-plus-square',
						'library' => 'fa-solid',
					),
					'condition'        => array(
						'ip_icon_switcher'  => 'yes',
						'ip_image_switcher' => '',
					),
				)
			);

			$this->add_control(
				'ip_image_content',
				array(
					'label'     => esc_html__( "Choix de l'image", 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array(
						'active' => true,
					),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'condition' => array( 'ip_image_switcher' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'ip_image',
					'default'   => 'medium',
					'exclude'   => array( 'custom' ),
					'condition' => array( 'ip_image_switcher' => 'yes' ),
				)
			);

			$this->add_responsive_control(
				'ip_image_height',
				array(
					'label'                => esc_html__( 'Hauteur', 'eac-components' ),
					'type'                 => Controls_Manager::SLIDER,
					'size_units'           => array( 'px' ),
					'default'              => array(
						'size' => 300,
						'unit' => 'px',
					),
					'tablet_default'       => array(
						'size' => 230,
						'unit' => 'px',
					),
					'mobile_default'       => array(
						'size' => 250,
						'unit' => 'px',
					),
					'tablet_extra_default' => array(
						'size' => 250,
						'unit' => 'px',
					),
					'mobile_extra_default' => array(
						'size' => 180,
						'unit' => 'px',
					),
					'range'                => array(
						'px' => array(
							'min'  => 50,
							'max'  => 1000,
							'step' => 10,
						),
					),
					'selectors'            => array( '{{WRAPPER}} .ip-image img' => 'block-size: {{SIZE}}{{UNIT}};' ),
					'condition'            => array( 'ip_image_switcher' => 'yes' ),
				)
			);

			$this->add_control(
				'ip_image_align',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'    => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => 'start',
						'right'  => 'end',
					),
					'selectors' => array(
						'{{WRAPPER}} .ip-image' => 'text-align: {{VALUE}};',
					),
					'default'   => 'center',
					'condition' => array( 'ip_image_switcher' => 'yes' ),
				)
			);

			$this->add_control(
				'ip_lightbox',
				array(
					'label'        => esc_html__( 'Visionneuse', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'ip_image_switcher' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_titre_content',
			array(
				'label' => esc_html__( 'Titre', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ip_title',
				array(
					'label'       => esc_html__( 'Titre', 'eac-components' ),
					'default'     => esc_html__( 'Votre titre', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'ip_title_tag',
				array(
					'label'     => esc_html__( 'Étiquette', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'h2',
					'options'   => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'P',
					),
					'condition' => array( 'ip_title!' => '' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_carac_content',
			array(
				'label' => esc_html__( 'Caractéristiques', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->add_control(
				'ip_carac_item',
				array(
					'label'       => esc_html__( 'Texte', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Caractéristiques', 'eac-components' ),
					'placeholder' => esc_html__( 'Caractéristiques', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$repeater->add_control(
				'ip_carac_inclus',
				array(
					'label'        => esc_html__( 'Élément inclus', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'ip_carac_list',
				array(
					'label'       => esc_html__( 'Liste des caractéristiques', 'eac-components' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'ip_carac_item' => esc_html__( 'Élément liste #1', 'eac-components' ),
						),
						array(
							'ip_carac_item' => esc_html__( 'Élément liste #2', 'eac-components' ),
						),
						array(
							'ip_carac_item' => esc_html__( 'Élément liste #3', 'eac-components' ),
						),
						array(
							'ip_carac_item' => esc_html__( 'Élément liste #4', 'eac-components' ),
						),
					),
					'title_field' => '{{{ ip_carac_item }}}',
					'button_text' => esc_html__( 'Ajouter une caractéristique', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_price_content',
			array(
				'label' => esc_html__( 'Prix du produit', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ip_price',
				array(
					'label'       => esc_html__( 'Prix de vente', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'XXX €', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_section_button',
			array(
				'label' => esc_html__( 'Bouton', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ip_button_text',
				array(
					'label'   => esc_html__( 'Texte', 'eac-components' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'En savoir plus', 'eac-components' ),
					'ai'      => array( 'active' => false ),
				)
			);

			$this->add_control(
				'ip_link_to',
				array(
					'label'   => esc_html__( 'Type de lien', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => array(
						'none'   => esc_html__( 'Aucun', 'eac-components' ),
						'custom' => esc_html__( 'URL', 'eac-components' ),
						'file'   => esc_html__( 'Fichier média', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'ip_link_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array( 'ip_link_to' => 'custom' ),
				)
			);

			$this->add_control(
				'ip_link_page',
				array(
					'label'     => esc_html__( 'Lien de page', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => Eac_Tools_Util::get_pages_by_name(),
					'condition' => array( 'ip_link_to' => 'file' ),

				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_global_settings',
			array(
				'label' => esc_html__( 'Ruban', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ip_ribbon_switcher',
				array(
					'label'        => esc_html__( 'Ajouter un ruban', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'ip_ribbon_text',
				array(
					'label'       => esc_html__( 'Texte', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'default'     => esc_html__( 'Ruban', 'eac-components' ),
					'condition'   => array( 'ip_ribbon_switcher' => 'yes' ),
				)
			);

			$this->add_control(
				'ip_ribbon_position',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'right'    => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => 'start',
						'right'  => 'end',
					),
					'default'   => 'left',
					'condition' => array( 'ip_ribbon_switcher' => 'yes' ),
				)
			);

			$this->add_responsive_control(
				'ip_ribbon_margin',
				array(
					'label'      => esc_html__( 'Position (px)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 35,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 30,
							'max'  => 85,
							'step' => 5,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .image-ribbon-inner' => 'margin-block-start: {{SIZE}}px; transform:translate(calc(-50% + {{SIZE}}px), -50%) rotate(-45deg);',
						'html[dir="rtl"] {{WRAPPER}} .image-ribbon-inner' => 'margin-block-start: {{SIZE}}px; transform:translate(calc(50% - {{SIZE}}px), -50%) rotate(45deg);',
					),
					'condition'  => array( 'ip_ribbon_switcher' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_bg_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'ip_box_width',
			array(
				'label'          => esc_html__( 'Largeur', 'eac-components' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px', '%' ),
				'default'        => array(
					'size' => 100,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'range'          => array(
					'px' => array(
						'min'  => 50,
						'max'  => 1140,
						'step' => 50,
					),
					'%'  => array(
						'min'  => 5,
						'max'  => 100,
						'step' => 5,
					),
				),
				'label_block'    => true,
				'selectors'      => array(
					'{{WRAPPER}} .ip-wrapper' => 'inline-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
			$this->add_control(
				'ip_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .ip-wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'ip_box_border',
					'selector' => '{{WRAPPER}} .ip-wrapper',
				)
			);

			$this->add_control(
				'ip_bg_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .ip-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'ip_box_shadow',
					'exclude'  => array(
						'box_shadow_position',
					),
					'selector' => '{{WRAPPER}} .ip-wrapper',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_icone_style',
			array(
				'label'     => esc_html__( 'Pictogramme', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'ip_icon_switcher'   => 'yes',
					'ip_image_switcher!' => 'yes',
				),
			)
		);

			$this->add_control(
				'ip_icone_voir',
				array(
					'label'        => esc_html__( 'Afficher', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'default' => esc_html__( 'Défaut', 'eac-components' ),
						'stacked' => esc_html__( 'Empilé', 'eac-components' ),
						'framed'  => esc_html__( 'Encadré', 'eac-components' ),
					),
					'default'      => 'default',
					'prefix_class' => 'elementor-view-',
				)
			);

			$this->add_control(
				'ip_icone_forme',
				array(
					'label'        => esc_html__( 'Forme', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'circle' => esc_html__( 'Ronde', 'eac-components' ),
						'square' => esc_html__( 'Carrée', 'eac-components' ),
					),
					'default'      => 'circle',
					'condition'    => array( 'ip_icone_voir!' => 'default' ),
					'prefix_class' => 'elementor-shape-',
				)
			);

			$this->add_control(
				'ip_icone_align',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'    => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => 'start',
						'right'  => 'end',
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array( '{{WRAPPER}} .ip-icone-wrapper' => 'text-align: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'ip_icone_size',
				array(
					'label'                => esc_html__( 'Dimension', 'eac-components' ),
					'type'                 => Controls_Manager::SLIDER,
					'size_units'           => array( 'px' ),
					'default'              => array(
						'size' => 60,
						'unit' => 'px',
					),
					'tablet_default'       => array(
						'size' => 40,
						'unit' => 'px',
					),
					'mobile_default'       => array(
						'size' => 50,
						'unit' => 'px',
					),
					'tablet_extra_default' => array(
						'size' => 40,
						'unit' => 'px',
					),
					'mobile_extra_default' => array(
						'size' => 50,
						'unit' => 'px',
					),
					'range'                => array(
						'px' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'            => array( '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_control(
				'ip_icone_couleur',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#e2bc74',
					'selectors' => array(
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; fill: {{VALUE}}; border-color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_title_style',
			array(
				'label'     => esc_html__( 'Titre', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ip_title!' => '' ),
			)
		);

			$this->add_control(
				'ip_titre_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .ip-title'       => 'color: {{VALUE}};',
						'{{WRAPPER}} .ip-title:after' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ip_title_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .ip-title',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_carac_style',
			array(
				'label' => esc_html__( 'Caractéristiques', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ip_carac_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .ip-description .ip-description-item > span > span,
						{{WRAPPER}} .ip-description .ip-description-item > span > span a,
						{{WRAPPER}} .woocommerce .star-rating:before,
						{{WRAPPER}} .woocommerce .star-rating span:before' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ip_carac_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .ip-description .ip-description-item',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_prix_style',
			array(
				'label' => esc_html__( 'Prix du produit', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ip_prix_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .ip-prix' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ip_prix_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .ip-prix',
				)
			);

			$this->add_control(
				'ip_prix_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .ip-prix' => 'background-color: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ip_button_style',
			array(
				'label' => esc_html__( 'Bouton', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ip_bouton_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .ip-button' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ip_bouton_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .ip-button',
				)
			);

			$this->add_control(
				'ip_bouton_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .ip-button' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'ip_bouton_shadow',
					'label'    => esc_html__( "Effet d'ombre", 'eac-components' ),
					'exclude'  => array(
						'box_shadow_position',
					),
					'selector' => '{{WRAPPER}} .ip-button',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ribbon_section_style',
			array(
				'label'     => esc_html__( 'Ruban', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ip_ribbon_switcher' => 'yes' ),
			)
		);

			$this->add_control(
				'ip_ribbon_inner_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFF',
					'selectors' => array( '{{WRAPPER}} .image-ribbon-inner' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ip_typography_ribbon_texte',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .image-ribbon-inner',
				)
			);

			$this->add_control(
				'ip_ribbon_inner_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#e2bc74',
					'selectors' => array( '{{WRAPPER}} .image-ribbon-inner' => 'background-color: {{VALUE}};' ),
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
		$settings = $this->get_settings_for_display();
		?>
		<div class="eac-image-promo">
			<div class='ip-wrapper'>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
		<?php
	}

	protected function render_galerie() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'ip-icone-wrapper' );

		// Il y a un titre ?
		$ip_title = $settings['ip_title'] ? $settings['ip_title'] : false;

		/** Formate le titre avec son tag */
		$title_tag = ! empty( $settings['ip_title_tag'] ) ? Utils::validate_html_tag( $settings['ip_title_tag'] ) : 'div';

		// Visionneuse pour l'image ?
		$visionneuse = 'yes' === $settings['ip_lightbox'] ? true : false;

		// Le ribbon est affiché
		$has_ribbon = 'yes' === $settings['ip_ribbon_switcher'] ? true : false;
		$image_url  = '';
		$link_url   = '';

		// l'image src, sa class ainsi que les attributs ALT et TITLE
		if ( ! empty( $settings['ip_image_content']['url'] ) ) {
			$image_url = esc_url( $settings['ip_image_content']['url'] );
			$this->add_render_attribute( 'ip_image_content', 'src', $image_url );
			$image_alt = Control_Media::get_image_alt( $settings['ip_image_content'] );
			$this->add_render_attribute( 'ip_image_content', 'alt', $image_alt );
			$this->add_render_attribute( 'ip_image_content', 'title', Control_Media::get_image_title( $settings['ip_image_content'] ) );
		}

		// Ajout de la class sur la div du bouton,
		$this->add_render_attribute( 'wrapper_button', 'class', 'ip-button-wrapper' );
		$this->add_render_attribute( 'button', 'class', 'ip-button eac-icon-svg elementor-button' );
		$this->add_render_attribute( 'button', 'role', 'button' );

		/** Il y a un lien sur le bouton */
		if ( 'custom' === $settings['ip_link_to'] && ! empty( $settings['ip_link_url']['url'] ) ) {
			$link_url = esc_url( $settings['ip_link_url']['url'] );
			$this->add_link_attributes( 'ip-link-to', $settings['ip_link_url'] );

			if ( $settings['ip_link_url']['is_external'] ) {
				$this->add_render_attribute( 'ip-link-to', 'rel', 'noopener noreferrer' );
			}
		} elseif ( 'file' === $settings['ip_link_to'] && ! empty( $settings['ip_link_page'] ) ) {
			global $wpdb;
			$pageid   = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->prefix}posts p WHERE p.post_title = %s",
					$settings['ip_link_page']
				)
			);
			$link_url = $settings['ip_link_page'];
			$this->add_render_attribute( 'ip-link-to', 'href', esc_url( get_permalink( $pageid ) ) );
		}

		// Visionneuse demandée sur l'image sélectionnée
		if ( $visionneuse && ! empty( $image_url ) ) {
			// Url de l'image et on force la visionneuse Elementor à 'no'
			$this->add_render_attribute(
				'ip-lightbox',
				array(
					'href'                         => $image_url,
					'data-elementor-open-lightbox' => 'no',
				)
			);
			$this->add_render_attribute( 'ip-lightbox', 'data-fancybox', 'ip-gallery' );
			$this->add_render_attribute( 'ip-lightbox', 'data-caption', $image_alt ); // Caption pour la fancybox
		}

		// la position du ribbon
		if ( $has_ribbon ) {
			$this->add_render_attribute( 'ribbon', 'class', 'image-ribbon image-ribbon-' . esc_attr( $settings['ip_ribbon_position'] ) );
		}
		?>

		<!-- Ribbon + lightbox -->
		<?php if ( $has_ribbon ) : ?>
			<?php if ( $visionneuse && ! empty( $image_url ) ) : ?>
				<a <?php $this->print_render_attribute_string( 'ip-lightbox' ); ?>>
			<?php endif; ?>
			<span <?php $this->print_render_attribute_string( 'ribbon' ); ?>>
				<span class='image-ribbon-inner'><?php echo esc_html( $settings['ip_ribbon_text'] ); ?></span>
			</span>
			<?php if ( $visionneuse && ! empty( $image_url ) ) : ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<!-- Image + lightbox -->
		<?php if ( ! empty( $image_url ) ) : ?>
			<figure class='ip-image'>
				<?php if ( $visionneuse ) : ?>
					<a <?php $this->print_render_attribute_string( 'ip-lightbox' ); ?>>
				<?php endif; ?>
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ip_image', 'ip_image_content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $visionneuse ) : ?>
					</a>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<!-- Affichage d'une icone -->
		<?php if ( ! empty( $settings['ip_icon_content_new'] ) ) : ?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<div class='ip-icone elementor-icon'>
					<?php Icons_Manager::render_icon( $settings['ip_icon_content_new'], array( 'aria-hidden' => 'true' ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="ip-wrapper__inner_content">
			<!-- Affichage du titre -->
			<?php if ( $ip_title ) :
				$this->add_inline_editing_attributes( 'ip_title', 'none' );
				$this->add_render_attribute( 'ip_title', 'class', 'ip-title' );
				echo '<' . esc_attr( $title_tag ) . ' ' . $this->get_render_attribute_string( 'ip_title' ) . '>' . esc_html( $settings['ip_title'] ) . '</' . esc_attr( $title_tag ) . '>';
			endif; ?>

			<!-- Affichage des caractéristiques -->
			<?php
			if ( count( $settings['ip_carac_list'] ) ) {
				ob_start();
				Icons_Manager::render_icon(
					array(
						'value'   => 'fas fa-check',
						'library' => 'fa-solid',
					),
					array( 'aria-hidden' => 'true' )
				);
				$icon_check = ob_get_clean();
				ob_start();
				Icons_Manager::render_icon(
					array(
						'value'   => 'fas fa-times',
						'library' => 'fa-solid',
					),
					array( 'aria-hidden' => 'true' )
				);
				$icon_times = ob_get_clean();
				?>
				<div class='ip-description woocommerce'>
					<div class='ip-description-item'>
						<?php
						foreach ( $settings['ip_carac_list'] as $index => $item ) {
							$repeater_setting_key  = $this->get_repeater_setting_key( 'ip_carac_item', 'ip_carac_list', $index );
							$this->add_render_attribute( $repeater_setting_key, 'class', 'ip-description__item-label' );
							$this->add_inline_editing_attributes( $repeater_setting_key );
							$icone = 'yes' === $item['ip_carac_inclus'] ? $icon_check : $icon_times; ?>
							<span class='ip-description__item'>
								<span class='ip-description__item-icon eac-icon-svg'><?php echo wp_kses_post( $icone ); ?></span>
								<span <?php $this->print_render_attribute_string( $repeater_setting_key ); ?>><?php echo esc_html( $item['ip_carac_item'] ); ?></span>
							</span>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

			<!-- Affichage du prix -->
			<?php if ( ! empty( $settings['ip_price'] ) ) :
				$this->add_inline_editing_attributes( 'ip_price', 'none' );
				$this->add_render_attribute( 'ip_price', 'class', 'ip-prix' );
				?>
				<div <?php $this->print_render_attribute_string( 'ip_price' ); ?>><?php echo esc_html( $settings['ip_price'] ); ?></div>
			<?php endif; ?>

			<!-- Affichage du bouton linker -->
			<?php $this->add_inline_editing_attributes( 'ip_button_text', 'none' ); ?>
			<div <?php $this->print_render_attribute_string( 'wrapper_button' ); ?>>
				<?php if ( ! empty( $link_url ) ) : ?>
					<a  <?php $this->print_render_attribute_string( 'ip-link-to' ); ?>>
					<?php endif; ?>
					<span <?php $this->print_render_attribute_string( 'button' ); ?>>
						<?php
						Icons_Manager::render_icon(
							array(
								'value'   => 'fas fa-arrow-right',
								'library' => 'fa-solid',
							),
							array( 'aria-hidden' => 'true' )
						); ?>
						<span <?php $this->print_render_attribute_string( 'ip_button_text' ); ?>><?php echo esc_html( $settings['ip_button_text'] ); ?></span>
					</span>
				<?php if ( ! empty( $link_url ) ) : ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
