<?php
/**
 * Class: Site_Search_Widget
 * Name: Rechercher
 * Slug: eac-addon-site-search
 *
 * Description: Affichage du formulaire de recherche
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Site_Search_Widget extends Widget_Base {
	use \EACCustomWidgets\Includes\Widgets\Traits\Shared_Icon_Svg_Trait;

	/**
	 * Constructeur de la class
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		EAC_Plugin::instance()->register_script( 'eac-site-search', 'includes/templates-lib/assets/js/site-search', array( 'jquery', 'elementor-frontend' ), '2.1.0',
			array(
				'strategy' => 'defer',
				'in_footer' => true,
			)
		);
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-search';

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
		return array( 'eac-site-search' );
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
		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'site_search_settings_fields',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ss_content_placeholder',
				array(
					'label'   => esc_html__( 'Texte suggéré', 'eac-components' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'Rechercher', 'eac-components' ),
					'dynamic' => array( 'active' => true ),
				)
			);

			$this->add_responsive_control(
				'ss_content_width',
				array(
					'label'          => esc_html__( 'Largeur (%)', 'eac-components' ),
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
						'px' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}} .eac-search_form-wrapper' => 'inline-size: {{SIZE}}%;',
					),
				)
			);

			$this->add_responsive_control(
				'ss_button_icon_align',
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
					'selectors'            => array(
						'{{WRAPPER}} .eac-search_form-wrapper,
						{{WRAPPER}} .eac-search_form-wrapper button[type="button"].eac-search_button-toggle' => 'margin-block: 0; margin-inline: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_button_hidden',
				array(
					'label'     => esc_html__( 'Cacher le bouton', 'eac-components' ),
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
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ss_button_position',
				array(
					'label'     => esc_html__( 'Position du bouton', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'left',
					'toggle'    => false,
					'condition' => array( 'ss_button_hidden' => 'no' ),
				)
			);

			$this->add_control(
				'ss_button_icon',
				array(
					'label'       => esc_html__( 'Icône', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => 'true',
					'default'     => array(
						'value'   => 'fas fa-search',
						'library' => 'fa-solid',
					),
					'skin'        => 'inline',
					'condition'   => array( 'ss_button_hidden' => 'no' ),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'ss_text_field_style',
			array(
				'label' => esc_html__( 'Champ de saisie', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ss_text_field_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-input' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_text_field_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_form-input',
				)
			);

			$this->add_control(
				'ss_text_field_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_form-input' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_text_field_icon_color',
				array(
					'label'     => esc_html__( 'Couleur des icônes', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-container svg' => 'fill: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ss_button_style',
			array(
				'label'     => esc_html__( 'Bouton', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ss_button_hidden' => 'no' ),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_style_button_typo',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_button-toggle',
				)
			);

			$this->add_control(
				'ss_style_button_color',
				array(
					'label'     => esc_html__( "Couleur de l'icône", 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor_hover',
				array(
					'label'     => esc_html__( 'Couleur du fond au survol', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle:hover, {{WRAPPER}} .eac-search_button-toggle:focus' => 'background-color: {{VALUE}};',
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
		$settings         = $this->get_settings_for_display();
		$is_button_hidden = 'yes' === $settings['ss_button_hidden'] ? true : false;
		$this->add_render_attribute(
			'input',
			array(
				'placeholder'     => sanitize_text_field( $settings['ss_content_placeholder'] ),
				'class'           => 'eac-search_form-input',
				'type'            => 'search',
				'name'            => 's',
				'id'              => 'eac-search',
				'aria-labelledby' => 'eac-search-label',
				'value'           => get_search_query(),
			)
		);
		$this->add_render_attribute(
			'wrapper',
			array(
				'class'            => 'eac-search_form-wrapper',
				'data-hide-button' => $is_button_hidden,
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( ! $is_button_hidden && 'left' === $settings['ss_button_position'] ) { ?>
				<button class='eac-search_button-toggle' type='button' aria-expanded='false' aria-controls='eac-search_form' aria-label="<?php esc_html_e( 'Formulaire de recherche', 'eac-components' ); ?>">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			<?php } ?>
			<div class='eac-search_select-wrapper'>
				<select class='eac-search_select-post-type' name='eac_advanced' id='eac_advanced' aria-label="<?php esc_html_e( "Rechercher: filtre par type d'article", 'eac-components' ); ?>">
					<option value='any'><?php esc_html_e( 'Tous', 'eac-components' ); ?></option>
					<option value='page'><?php esc_html_e( 'Page', 'eac-components' ); ?></option>
					<option value='post'><?php esc_html_e( 'Article', 'eac-components' ); ?></option>
					<?php if ( class_exists( 'WooCommerce' ) ) { ?>
						<option value='product'><?php esc_html_e( 'Produit', 'eac-components' ); ?></option>
					<?php } ?>
				</select>
			</div>
			<form id='eac-search_form' class='eac-search_form' role='search' action='<?php echo esc_url( home_url() ); ?>' method='get'>
				<input class='eac-search_form-post-type' type='hidden' name='post_type' value='any' />
				<div class='eac-search_form-container'>
					<label id='eac-search-label' for='eac-search' class='visually-hidden'>Label for search field</label>
					<input <?php $this->print_render_attribute_string( 'input' ); ?>>
					<span class='search-icon'> <!-- Les icones SVG sont safe -->
						<?php echo $this->get_svg_icon_search(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class='clear-icon' tabindex='0'>
						<?php echo $this->get_svg_icon_clear(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</div>
			</form>
			<?php
			if ( ! $is_button_hidden && 'right' === $settings['ss_button_position'] ) { ?>
				<button class='eac-search_button-toggle' type='button' aria-expanded='false' aria-controls='eac-search_form' aria-label="<?php esc_html_e( 'Ouvrir le formulaire de recherche', 'eac-components' ); ?>">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render page title output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {}
}
