<?php
/**
 * Class: Social_Media_Widget
 * Name: Réseaux sociaux
 * Slug: eac-addon-social-media
 *
 * Description: Affiche la liste des réseaux sociaux
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Icons_Manager;

class Social_Media_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'social-media';

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
	 * @since 1.7.0
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

		$this->start_controls_section(
			'social_media_settings',
			array(
				'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'social_media_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( "Le contenu n'est mis à jour que sur le frontend", 'eac-components' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'social_media_email',
				array(
					'label'       => esc_html__( 'Email', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_email_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fas fa-envelope',
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_control(
				'social_media_phone',
				array(
					'label'       => esc_html__( 'Téléphone', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_phone_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fas fa-phone-alt',
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_control(
				'social_media_url',
				array(
					'label'       => esc_html__( 'Site Web', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_url_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fas fa-globe',
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_control(
				'social_media_facebook',
				array(
					'label'       => 'Facebook',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_facebook_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-facebook-f',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_flickr',
				array(
					'label'       => 'Flickr',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_flickr_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-flickr',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_github',
				array(
					'label'       => 'Github',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_github_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-github',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_instagram',
				array(
					'label'       => 'Instagram',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_instagram_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-instagram',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_linkedin',
				array(
					'label'       => 'Linkedin',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_linkedin_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-linkedin',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_mastodon',
				array(
					'label'       => 'Mastodon',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_mastodon_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-mastodon',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_pinterest',
				array(
					'label'       => 'Pinterest',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_pinterest_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-pinterest',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_quora',
				array(
					'label'       => 'Quora',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_quora_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-quora',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_reddit',
				array(
					'label'       => 'Reddit',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_reddit_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-reddit',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_spotify',
				array(
					'label'       => 'Spotify',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_spotify_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-spotify',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_telegram',
				array(
					'label'       => 'Telegram',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_telegram_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-telegram',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_tiktok',
				array(
					'label'       => 'Tiktok',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_tiktok_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-tiktok',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_tumblr',
				array(
					'label'       => 'Tumblr',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_tumblr_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-tumblr',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_twitch',
				array(
					'label'       => 'Twitch',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_twitch_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-twitch',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_twitter',
				array(
					'label'       => 'Twitter',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_twitter_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-x-twitter',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_youtube',
				array(
					'label'       => 'Youtube',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_youtube_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-youtube',
						'library' => 'fa-brands',
					),
				)
			);

			$this->add_control(
				'social_media_whatsapp',
				array(
					'label'       => 'WhatsApp',
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::URL_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '#',
					'separator'   => 'before',
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'social_media_whatsapp_icon',
				array(
					'label'       => esc_html__( 'Pictogramme', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'default'     => array(
						'value'   => 'fab fa-whatsapp',
						'library' => 'fa-brands',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'social_media_general_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'social_media_label',
				array(
					'label'     => esc_html__( 'Ajouter le label des réseaux sociaux', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'no',
					'toggle'    => false,
				)
			);

			$this->add_responsive_control(
				'social_media_width',
				array(
					'label'          => esc_html__( 'Largeur du conteneur (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 100,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
					),
					'range'          => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'      => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'inline-size:{{SIZE}}%;' ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'social_media_wrapper_align',
				array(
					'label'                => esc_html__( 'Alignement', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'default'              => 'center',
					'options'              => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => '0 auto',
						'center' => 'auto',
						'right'  => 'auto 0',
					),
					'selectors'            => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'margin-block: 0; margin-inline: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'social_media_icon_space_h',
				array(
					'label'       => esc_html__( 'Espacement horizontal', 'eac-components' ),
					'description' => esc_html__( 'Espacement horizontal entre les icônes', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-h',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-h',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-h',
						),
					),
					'default'     => 'space-around',
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'justify-content: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'social_media_global_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'social_media_global_container',
				array(
					'label'     => esc_html__( 'Conteneur', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'social_media_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'social_media_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'social_media_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'social_media_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'social_media_global_media',
				array(
					'label'     => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'social_media_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.1,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .dynamic-tags_social-container .dynamic-tags_social-icon',
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
		$settings      = $this->get_settings_for_display();
		$social_medias = Eac_Tools_Util::get_all_social_medias_icon();
		$add_label     = 'yes' === $this->get_settings( 'social_media_label' ) ? true : false;

		ob_start();
		foreach ( $social_medias as $site => $value ) {
			if ( empty( $settings[ 'social_media_' . $site ] ) || '#' === $settings[ 'social_media_' . $site ] ) {
				continue; }

			$label = $value['name'];
			$name  = ucfirst( $value['name'] );
			if ( 'email' === $site ) {
				$email     = sanitize_email( $settings[ 'social_media_' . $site ] );
				$email_obf = str_contains( $email, '@' ) ? explode( '@', $email )[0] . '#actus.' . explode( '@', $email )[1] : '';
				echo '<a class="eac-accessible-link obfuscated-link" href="#" data-link="' . esc_attr( $email_obf ) . '" rel="nofollow" aria-label="' . esc_attr( $name ) . '">';
			} elseif ( 'url' === $site ) {
				echo '<a class="eac-accessible-link" href="' . esc_url( $settings[ 'social_media_' . $site ] ) . '" rel="nofollow" aria-label="' . esc_html__( 'Voir le site web', 'eac-components' ) . '">';
			} elseif ( 'phone' === $site ) {
				$label     = $settings[ 'social_media_' . $site ];
				$url_phone = preg_replace( '/[^\d+]/', '', $settings[ 'social_media_' . $site ] ?? '' );
				echo '<a class="eac-accessible-link obfuscated-tel" href="#" data-link="#actus.' . esc_attr( $url_phone ) . '" aria-label="' . esc_attr( $name ) . '">';
			} else {
				echo '<a class="eac-accessible-link" href="' . esc_url( $settings[ 'social_media_' . $site ] ) . '" rel="nofollow" aria-label="' . esc_html__( 'Voir le réseau social', 'eac-components' ) . esc_attr( $name ) . '">';
			}
			echo '<span class="dynamic-tags_social-icon eac-icon-svg ' . esc_attr( $site ) . '">';
			Icons_Manager::render_icon( $settings[ 'social_media_' . $site . '_icon' ], array( 'aria-hidden' => 'true' ) );
			if ( $add_label ) {
				echo '<span dir="ltr" class="dynamic-tags_social-label ' . esc_attr( $site ) . '">' . esc_html( ucfirst( $label ) ) . '</span>';
			}
			echo '</span></a>';
		}
		$output = ob_get_clean();

		if ( ! empty( $output ) ) {
			echo '<div class="dynamic-tags_social-container">';
			echo wp_kses_post( $output );
			echo '</div>';
		}
	}

	protected function content_template() {}
}
