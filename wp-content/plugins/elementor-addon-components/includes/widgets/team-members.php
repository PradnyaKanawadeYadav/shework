<?php
/**
 * Class: Team_Members_Widget
 * Name: Membres de l'équipe
 * Slug: eac-addon-team-members
 *
 * Description: Affiche la liste des membres d'une équipe avec leur photo, leur bio et les réseaux sociaux
 * 6 habillages différents peuvent être appliqués ansi qu'une multitude de paramétrages
 *
 * @since 1.9.1
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Utils\Eac_Helpers_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;
use Elementor\Utils;

class Team_Members_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Team_Members_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-team-members', EAC_Plugin::instance()->get_style_url( 'assets/css/team-members' ), array( 'eac-frontend' ), '1.9.1' );
	}

	/**
	 * La taille de l'image par défaut
	 *
	 * @var IMAGE_SIZE
	 *
	 */
	const IMAGE_SIZE = '300';

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'team-members';

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
		return array( 'eac-team-members' );
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

		// Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		$this->start_controls_section(
			'tm_members_settings',
			array(
				'label' => esc_html__( 'Liste des membres', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'tm_member_tabs_settings' );

				$repeater->start_controls_tab(
					'tm_member_skills_settings',
					array(
						'label' => esc_html__( 'Membre', 'eac-components' ),
					)
				);

					$repeater->add_control(
						'tm_member_image',
						array(
							'label'   => esc_html__( 'Image', 'eac-components' ),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => array( 'active' => true ),
							'default' => array(
								'url' => Utils::get_placeholder_image_src(),
							),
						)
					);

					$repeater->add_control(
						'tm_member_name',
						array(
							'label'       => esc_html__( 'Nom', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'ai'          => array( 'active' => false ),
							'default'     => 'John Doe',
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'tm_member_title',
						array(
							'label'       => esc_html__( 'Intitulé du poste', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'ai'          => array( 'active' => false ),
							'default'     => esc_html__( 'Développeur', 'eac-components' ),
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'tm_member_biography',
						array(
							'label'       => esc_html__( 'Biographie', 'eac-components' ),
							'type'        => Controls_Manager::TEXTAREA,
							'default'     => esc_html__( "Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components' ),
							'label_block' => true,
						)
					);

				$repeater->end_controls_tab();

				$repeater->start_controls_tab(
					'tm_member_social_settings',
					array(
						'label' => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					)
				);

					$this->add_control(
						'tm_member_social_info',
						array(
							'type'            => Controls_Manager::RAW_HTML,
							'raw'             => esc_html__( "Le contenu n'est mis à jour que sur le frontend", 'eac-components' ),
							'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						)
					);

					$repeater->add_control(
						'tm_member_social_email',
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

					$repeater->add_control(
						'tm_member_social_email_icon',
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

					$repeater->add_control(
						'tm_member_social_phone',
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

					$repeater->add_control(
						'tm_member_social_phone_icon',
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

					$repeater->add_control(
						'tm_member_social_url',
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

					$repeater->add_control(
						'tm_member_social_url_icon',
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

					$repeater->add_control(
						'tm_member_social_facebook',
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

					$repeater->add_control(
						'tm_member_social_facebook_icon',
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

					$repeater->add_control(
						'tm_member_social_flickr',
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

					$repeater->add_control(
						'tm_member_social_flickr_icon',
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

					$repeater->add_control(
						'tm_member_social_github',
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

					$repeater->add_control(
						'tm_member_social_github_icon',
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

					$repeater->add_control(
						'tm_member_social_instagram',
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

					$repeater->add_control(
						'tm_member_social_instagram_icon',
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

					$repeater->add_control(
						'tm_member_social_linkedin',
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

					$repeater->add_control(
						'tm_member_social_linkedin_icon',
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

					$repeater->add_control(
						'tm_member_social_mastodon',
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

					$repeater->add_control(
						'tm_member_social_mastodon_icon',
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

					$repeater->add_control(
						'tm_member_social_pinterest',
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

					$repeater->add_control(
						'tm_member_social_pinterest_icon',
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

					$repeater->add_control(
						'tm_member_social_quora',
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

					$repeater->add_control(
						'tm_member_social_quora_icon',
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

					$repeater->add_control(
						'tm_member_social_reddit',
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

					$repeater->add_control(
						'tm_member_social_reddit_icon',
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

					$repeater->add_control(
						'tm_member_social_spotify',
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

					$repeater->add_control(
						'tm_member_social_spotify_icon',
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

					$repeater->add_control(
						'tm_member_social_telegram',
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

					$repeater->add_control(
						'tm_member_social_telegram_icon',
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

					$repeater->add_control(
						'tm_member_social_tiktok',
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

					$repeater->add_control(
						'tm_member_social_tiktok_icon',
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

					$repeater->add_control(
						'tm_member_social_tumblr',
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

					$repeater->add_control(
						'tm_member_social_tumblr_icon',
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

					$repeater->add_control(
						'tm_member_social_twitch',
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

					$repeater->add_control(
						'tm_member_social_twitch_icon',
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

					$repeater->add_control(
						'tm_member_social_twitter',
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

					$repeater->add_control(
						'tm_member_social_twitter_icon',
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

					$repeater->add_control(
						'tm_member_social_youtube',
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

					$repeater->add_control(
						'tm_member_social_youtube_icon',
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

					$repeater->add_control(
						'tm_member_social_whatsapp',
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

					$repeater->add_control(
						'tm_member_social_whatsapp_icon',
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

				$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'tm_member_list',
				array(
					'label'       => esc_html__( 'Liste des membres', 'eac-components' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'tm_member_name'  => 'John Doe',
							'tm_member_title' => esc_html__( 'Développeur PHP', 'eac-components' ),
						),
						array(
							'tm_member_name'  => 'Jane Doe',
							'tm_member_title' => esc_html__( 'Développeur JS', 'eac-components' ),
						),
						array(
							'tm_member_name'  => 'Jcb Doe',
							'tm_member_title' => esc_html__( 'Développeur CSS', 'eac-components' ),
						),
					),
					'title_field' => '{{{ tm_member_name }}}',
					'button_text' => esc_html__( 'Ajouter un membre', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'tm_general_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'tm_settings_layout',
			array(
				'label'     => esc_html__( 'Disposition', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

			$this->add_responsive_control(
				'tm_columns',
				array(
					'label'          => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
				)
			);

			$this->add_control(
				'tm_settings_member_style',
				array(
					'label'        => esc_html__( 'Habillage', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'skin-1',
					'options'      => array(
						'skin-1' => 'Skin 1',
						'skin-2' => 'Skin 2',
						'skin-3' => 'Skin 3',
						'skin-4' => 'Skin 4',
						'skin-7' => 'Skin 5',
						'skin-8' => 'Skin 6',
					),
					'prefix_class' => 'team-members_global-',
				)
			);

			$this->add_control(
				'tm_settings_name_tag',
				array(
					'label'   => esc_html__( 'Étiquette du nom', 'eac-components' ),
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

			$this->add_control(
				'tm_settings_title_tag',
				array(
					'label'   => esc_html__( "Étiquette de l'intitulé du poste", 'eac-components' ),
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

			$this->add_responsive_control(
				'tm_overlay_height',
				array(
					'label'      => esc_html__( "Hauteur de l'overlay (%)", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 80,
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.team-members_global-skin-2 .team-member_content:hover .team-member_wrapper-info,
						{{WRAPPER}}.team-members_global-skin-2 .team-member_content:focus .team-member_wrapper-info' => 'transform: translateY(calc(100% - {{SIZE}}%)) !important;',
					),
					'condition'  => array( 'tm_settings_member_style' => 'skin-2' ),
				)
			);

			$this->add_control(
				'tm_image_settings',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'tm_image_size',
					'default' => 'medium',
					'exclude' => array( 'custom' ),
				)
			);

			$this->add_control(
				'tm_image_shape',
				array(
					'label'        => esc_html__( 'Image ronde', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'round',
					'default'      => 'round',
					'prefix_class' => 'team-members_image-',
					'condition'    => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_responsive_control(
				'tm_image_width',
				array(
					'label'      => esc_html__( "Largeur de l'image", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'unit' => 'px',
						'size' => 150,
					),
					'range'      => array(
						'px' => array(
							'min'  => 50,
							'max'  => 500,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.team-members_global-skin-3 .team-member_content .team-member_image img' => 'inline-size:{{SIZE}}px; block-size:{{SIZE}}px;',
						'{{WRAPPER}}.team-members_global-skin-4 .team-member_content .team-member_image img' => 'inline-size:{{SIZE}}px; block-size:{{SIZE}}px;',
						'{{WRAPPER}}.team-members_global-skin-7 .team-member_content .team-member_image img' => 'inline-size:{{SIZE}}px; block-size:{{SIZE}}px;',
						'{{WRAPPER}}.team-members_global-skin-8 .team-member_content .team-member_image img' => 'inline-size:{{SIZE}}px; block-size:{{SIZE}}px;',
					),
					'condition'  => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			$this->add_control(
				'tm_enable_image_ratio',
				array(
					'label'       => esc_html__( 'Activer le ratio image', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'yes',
					'toggle'       => false,
					'condition'   => array( 'tm_settings_member_style' => array( 'skin-1', 'skin-2' ) ),
				)
			);

			$this->add_responsive_control(
				'tm_image_ratio',
				array(
					'label'          => esc_html__( 'Ratio', 'eac-components' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '1 / 1',
					'tablet_default' => '1 / 1',
					'mobile_default' => '9 / 16',
					'options'        => array(
						'1 / 1'  => esc_html__( 'Défaut', 'eac-components' ),
						'9 / 16' => esc_html( '9-16' ),
						'4 / 3'  => esc_html( '4-3' ),
						'3 / 2'  => esc_html( '3-2' ),
						'16 / 9' => esc_html( '16-9' ),
						'21 / 9' => esc_html( '21-9' ),
					),
					'selectors'   => array( '{{WRAPPER}} .team-member_image img' => 'aspect-ratio:{{SIZE}};' ),
					'condition'   => array(
						'tm_settings_member_style' => array( 'skin-1', 'skin-2' ),
						'tm_enable_image_ratio' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'tm_image_position_y',
				array(
					'label'      => esc_html__( 'Position verticale (%)', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 50,
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .team-member_content img' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'   => array(
						'tm_settings_member_style' => array( 'skin-1', 'skin-2' ),
						'tm_enable_image_ratio' => 'yes',
					),
				)
			);

			$this->add_control(
				'tm_image_animation',
				array(
					'label' => esc_html__( 'Animation', 'eac-components' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				)
			);

			$this->add_control(
				'tm_settings_social',
				array(
					'label'     => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'tm_settings_social_label',
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
				'tm_settings_social_width',
				array(
					'label'      => esc_html__( 'Largeur du conteneur', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 100,
					),
					'tablet_default'    => array(
						'unit' => '%',
					),
					'mobile_default'    => array(
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'inline-size:{{SIZE}}%;',
					),
				)
			);

			$this->add_control(
				'tm_settings_social_space_h',
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
					'default'     => 'space-between',
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'justify-content: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'tm_settings_content',
				array(
					'label'     => esc_html__( 'Disposition du contenu', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'tm_settings_alignment_v',
				array(
					'label'       => esc_html__( 'Alignement vertical', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Haut', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bas', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'toggle'      => false,
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .team-member_info-content' => 'justify-content: {{VALUE}};',
					),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'tm_settings_alignment_h',
				array(
					'label'     => esc_html__( 'Alignement horizontal', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'   => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'  => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array(
						'{{WRAPPER}} .team-member_info-content' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .team-member_biography' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'tm_section_global_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** Conteneur */
			$this->add_control(
				'ig_container_style',
				array(
					'label'     => esc_html__( 'Conteneur', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'tm_container_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .team-members_container' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Article */
			$this->add_control(
				'tm_items_section_style',
				array(
					'label'     => esc_html__( 'Articles', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'tm_global_style',
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
						'style-10' => 'Style 5',
						'style-11' => 'Style 6',
						'style-12' => 'Style 7',
					),
					'prefix_class' => 'team-member_wrapper-',
				)
			);

			$this->add_responsive_control(
				'tm_container_gap',
				array(
					'label'      => esc_html__( 'Marge entre les items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'px' => array(
							'size' => 10,
							'unit' => 'px',
						),
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .team-members_container' => 'gap: {{SIZE}}{{UNIT}}; padding-block: calc({{SIZE}}{{UNIT}} / 2); padding-inline: calc({{SIZE}}{{UNIT}} / 2);',
					),
				)
			);

			$this->add_control(
				'tm_global_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .team-member_content' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'tm_global_border',
					'selector'  => '{{WRAPPER}} .team-member_content',
					'condition' => array( 'tm_global_style' => 'style-0' ),
				)
			);

			$this->add_control(
				'tm_global_radius',
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
						'{{WRAPPER}} .team-member_content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'tm_global_style' => 'style-0' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'tm_global_shadow',
					'label'     => esc_html__( 'Ombre', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .team-member_content',
					'condition' => array( 'tm_global_style' => 'style-0' ),
				)
			);

			/** Image */
			$this->add_control(
				'tm_image_section_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'tm_image_style__border',
					'fields_options' => array(
						'border' => array( 'default' => 'solid' ),
						'width'  => array(
							'default' => array(
								'top'      => 5,
								'right'    => 5,
								'bottom'   => 5,
								'left'     => 5,
								'isLinked' => true,
							),
						),
						'color'  => array( 'default' => '#7fadc5' ),
					),
					'selector'       => '
						{{WRAPPER}}.team-members_global-skin-3 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-4 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-7 .team-member_content .team-member_image img,
						{{WRAPPER}}.team-members_global-skin-8 .team-member_content .team-member_image img',
					'condition' => array( 'tm_settings_member_style' => array( 'skin-3', 'skin-4', 'skin-7', 'skin-8' ) ),
				)
			);

			/** Nom */
			$this->add_control(
				'tm_name_section_style',
				array(
					'label'     => esc_html__( 'Nom', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'tm_name_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} .team-member_name .team-members_name-content' => 'color: {{VALUE}};',
						'{{WRAPPER}} .team-member_name:after' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_name_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.8,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .team-member_name .team-members_name-content',
				)
			);

			/** Poste */
			$this->add_control(
				'tm_job_section_style',
				array(
					'label'     => esc_html__( 'Intitulé du poste', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'tm_job_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'default'   => '#000000',
					'selectors' => array( '{{WRAPPER}} .team-member_title .team-members_title-content' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_job_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.2,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .team-member_title .team-members_title-content',
				)
			);

			/** Biographie */
			$this->add_control(
				'tm_biography_section_style',
				array(
					'label'     => esc_html__( 'Biographie', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'tm_biography_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'default'   => '#919CA7',
					'selectors' => array( '{{WRAPPER}} .team-member_biography p' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tm_biography_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector' => '{{WRAPPER}} .team-member_biography p',
				)
			);

			/** Réseaux sociaux */
			$this->add_control(
				'tm_icon_section_style',
				array(
					'label'     => esc_html__( 'Réseaux sociaux', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'tm_icon_typography',
					'label'          => esc_html__( 'Typographie', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'em',
								'size' => 1.5,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .dynamic-tags_social-container .dynamic-tags_social-icon',
				)
			);

			$this->add_control(
				'tm_style_social_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .dynamic-tags_social-container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_responsive_control(
				'tm_style_social_padding',
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
					'name'     => 'tm_style_social_border',
					'selector' => '{{WRAPPER}} .dynamic-tags_social-container',
				)
			);

			$this->add_control(
				'tm_style_social_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .dynamic-tags_social-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$id = $this->get_id();

		// Le wrapper du container
		$this->add_render_attribute( 'container_wrapper', 'class', 'team-members_container' );
		$this->add_render_attribute( 'container_wrapper', 'id', esc_attr( $id ) );
		?>
		<div class="eac-team-members">
			<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
				<?php $this->render_members(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_members() {
		$settings = $this->get_settings_for_display();

		/** Formate le nom avec son tag */
		$tag_name   = ! empty( $settings['tm_settings_name_tag'] ) ? Utils::validate_html_tag( $settings['tm_settings_name_tag'] ) : 'div';

		/** Formate le job avec son tag */
		$tag_title  = ! empty( $settings['tm_settings_title_tag'] ) ? Utils::validate_html_tag( $settings['tm_settings_title_tag'] ) : 'div';

		// La classe du titre/texte
		$this->add_render_attribute( 'content_wrapper', 'class', 'team-member_content' );

		// Boucle sur tous les items
		ob_start();
		foreach ( $settings['tm_member_list'] as $index => $item ) {
			$member_name_setting_key = $this->get_repeater_setting_key( 'tm_member_name', 'tm_member_list', $index );
			$this->add_inline_editing_attributes( $member_name_setting_key, 'none' );
			$this->add_render_attribute( $member_name_setting_key, 'class', 'team-members_name-content' );

			$member_title_setting_key = $this->get_repeater_setting_key( 'tm_member_title', 'tm_member_list', $index );
			$this->add_inline_editing_attributes( $member_title_setting_key, 'none' );
			$this->add_render_attribute( $member_title_setting_key, 'class', 'team-members_title-content' );

			$member_bio_setting_key = $this->get_repeater_setting_key( 'tm_member_biography', 'tm_member_list', $index );
			$this->add_inline_editing_attributes( $member_bio_setting_key, 'none' );
			$this->add_render_attribute( $member_bio_setting_key, 'dir', 'ltr' );

			// Il y a une Image
			if ( ! empty( $item['tm_member_image']['url'] ) ) {
				$attachment  = array();
				$name_with_tag    = '';
				$title_with_tag   = '';

				// Le nom
				if ( ! empty( $item['tm_member_name'] ) ) {
					$name_with_tag = '<' . $tag_name . ' ' . $this->get_render_attribute_string( $member_name_setting_key ) . '>' . esc_html( $item['tm_member_name'] ) . '</' . $tag_name . '>';
				}

				// Le job
				if ( ! empty( $item['tm_member_title'] ) ) {
					$title_with_tag = '<' . $tag_title . ' ' . $this->get_render_attribute_string( $member_title_setting_key ) . '>' . esc_html( $item['tm_member_title'] ) . '</' . $tag_title . '>';
				}

				/** L'image vient de la librarie des médias */
				if ( ! empty( $item['tm_member_image']['id'] ) ) {
					$attachment = Eac_Helpers_Util::wp_get_attachment_data( $item['tm_member_image']['id'], $settings['tm_image_size_size'] );
				} else { // Image avec Url externe
					$attachment['src']    = $item['tm_member_image']['url'];
					$attachment['width']  = self::IMAGE_SIZE;
					$attachment['height'] = self::IMAGE_SIZE;
					$attachment['alt']    = 'Team member image';
				}

				if ( ! $attachment || empty( $attachment ) ) {
					continue;
				}

				$this->add_render_attribute( 'tm_image', 'src', esc_url( $attachment['src'] ) );
				$this->add_render_attribute( 'tm_image', 'width', esc_attr( $attachment['width'] ) );
				$this->add_render_attribute( 'tm_image', 'height', esc_attr( $attachment['height'] ) );
				$this->add_render_attribute( 'tm_image', 'alt', esc_html( $attachment['alt'] ) );
				if ( isset( $attachment['srcset'] ) ) {
					$this->add_render_attribute( 'tm_image', 'srcset', esc_attr( $attachment['srcset'] ) );
					$this->add_render_attribute( 'tm_image', 'sizes', esc_attr( $attachment['srcsize'] ) );
				}
				if ( $settings['tm_image_animation'] ) {
					$this->add_render_attribute( 'tm_image', 'class', 'elementor-animation-' . $settings['tm_image_animation'] );
				}

				?>
				<div <?php $this->print_render_attribute_string( 'content_wrapper' ); ?>>
					<div class="team-member_image">
						<img <?php $this->print_render_attribute_string( 'tm_image' ); ?> />
					</div>
					<div class="team-member_wrapper-info">
						<div class="team-member_info-content">
							<?php if ( ! empty( $name_with_tag ) ) : ?>
								<div class="team-member_name">
									<?php echo $name_with_tag; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $title_with_tag ) ) : ?>
								<div class="team-member_title">
									<?php echo $title_with_tag; // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( $item['tm_member_biography'] ) ) : ?>
								<div class="team-member_biography">
									<p <?php $this->print_render_attribute_string( $member_bio_setting_key ); ?>><?php echo wp_kses_post( nl2br( sanitize_textarea_field( $item['tm_member_biography'] ) ) ); ?></p>
								</div>
							<?php endif; ?>
							<?php $this->get_social_medias( $item ); ?>
						</div>
					</div>
				</div>
				<?php
				$this->remove_render_attribute( 'tm_image' );
			}
		}
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * get_social_medias
	 *
	 * Render person social icons list
	 *
	 * @access protected
	 *
	 * @param object $repeater_item item courant du repeater
	 */
	private function get_social_medias( $repeater_item ) {
		$social_medias   = Eac_Tools_Util::get_all_social_medias_icon();
		$add_label       = 'yes' === $this->get_settings( 'tm_settings_social_label' ) ? true : false;

		ob_start();
		foreach ( $social_medias as $site => $value ) {
			if ( empty( $repeater_item['tm_member_name'] ) || empty( $repeater_item[ 'tm_member_social_' . $site ] ) || '#' === $repeater_item[ 'tm_member_social_' . $site ] ) {
				continue; }

			$label = $value['name'];
			$name  = ucfirst( $site ) . ' ' . esc_html__( 'de', 'eac-components' ) . ' ' . ucfirst( $repeater_item['tm_member_name'] );
			if ( 'email' === $site ) {
				$email     = sanitize_email( $repeater_item[ 'tm_member_social_' . $site ] );
				$email_obf = str_contains( $email, '@' ) ? explode( '@', $email )[0] . '#actus.' . explode( '@', $email )[1] : '';
				echo '<a class="eac-accessible-link obfuscated-link" href="#" data-link="' . esc_attr( $email_obf ) . '" rel="nofollow" aria-label="' . esc_attr( $name ) . '">';
			} elseif ( 'url' === $site ) {
				echo '<a class="eac-accessible-link" href="' . esc_url( $repeater_item[ 'tm_member_social_' . $site ] ) . '" rel="nofollow" aria-label="' . esc_html__( 'Voir le site web', 'eac-components' ) . '">';
			} elseif ( 'phone' === $site ) {
				$label     = $repeater_item[ 'tm_member_social_' . $site ];
				$url_phone = preg_replace( '/[^\d+]/', '', $repeater_item[ 'tm_member_social_' . $site ] ?? '' );
				echo '<a class="eac-accessible-link obfuscated-tel" href="#" data-link="#actus.' . esc_attr( $url_phone ) . '" aria-label="' . esc_attr( $name ) . '">';
			} else {
				echo '<a class="eac-accessible-link" href="' . esc_url( $repeater_item[ 'tm_member_social_' . $site ] ) . '" rel="nofollow" aria-label="' . esc_attr( $name ) . '">';
			}
			echo '<span class="dynamic-tags_social-icon eac-icon-svg ' . esc_attr( $site ) . '">';
			Icons_Manager::render_icon( $repeater_item[ 'tm_member_social_' . $site . '_icon' ], array( 'aria-hidden' => 'true' ) );
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
