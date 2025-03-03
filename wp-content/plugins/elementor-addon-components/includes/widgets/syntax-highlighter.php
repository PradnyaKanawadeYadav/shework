<?php
/**
 * Class: Syntax_Highlighter_Widget
 * Name: Surligneur de syntaxe
 * Slug: eac-addon-syntax-highlighter
 *
 * Description: Mise en relief de la syntaxe d'un code source dans différentes couleurs et polices (Thème)
 * relatif au language utilisé.
 *
 * @since 1.6.4
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Syntax_Highlighter_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Syntax_Highlighter_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-syntax-highlight', EAC_Plugin::instance()->get_style_url( 'assets/css/prism' ), array( 'eac-frontend' ), '1.6.4' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'syntax-highlight';

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
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-syntax-highlight' );
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

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'sh_syntax_highlighter',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'sh_syntax_language',
				array(
					'label'   => esc_html__( 'Langage', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'php',
					'options' => array(
						'css'        => 'CSS',
						'c'          => 'C',
						'cpp'        => 'C++',
						'csharp'     => 'C#',
						'html'       => 'HTML',
						'java'       => 'Java',
						'javascript' => 'Javascript',
						'json'       => 'JSON',
						'markdown'   => 'MD',
						'php'        => 'PHP',
						'python'     => 'Python',
						'rss'        => 'RSS',
						'sass'       => 'Sass',
						'scss'       => 'Scss',
						'sql'        => 'SQL',
						'svg'        => 'SVG',
						'twig'       => 'Twig',
						'xml'        => 'XML',
					),
				)
			);

			$this->add_control(
				'sh_syntax_linenumbers',
				array(
					'label'   => esc_html__( 'Numéros de ligne', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'yes',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'sh_syntax_code',
				array(
					'label'    => esc_html__( 'Code', 'eac-components' ),
					'type'     => Controls_Manager::CODE,
					'language' => 'text',
					'rows'     => 30,
					'dynamic'  => array(
						'active'     => true,
						'categories' => array(
							TagsModule::TEXT_CATEGORY,
						),
					),
				)
			);

		$this->end_controls_section();

		/** Style Section */
		$this->start_controls_section(
			'sh_general_style',
			array(
				'label' => esc_html__( 'Style', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'sh_syntax_height',
				array(
					'label'       => esc_html__( 'Hauteur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1500,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} pre[class*="language-"]' => 'max-height: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_control(
				'sh_syntax_theme',
				array(
					'label'   => esc_html__( 'Choix du thème', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => array(
						'default'        => esc_html__( 'Défaut', 'eac-components' ),
						'coy'            => 'Coy',
						'dark'           => 'Dark',
						'funky'          => 'Funky',
						'oceanic'        => 'Oceanic',
						'okaidia'        => 'Okaidia',
						'tomorrow-night' => 'Tomorrow-night',
						'twilight'       => 'Twilight',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'sh_syntax_typo',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array(
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					),
					'selector' => '{{WRAPPER}} pre[class*="language-"]',
				)
			);

			$this->add_control(
				'sh_syntax_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_PRIMARY,
					),
					'selectors' => array( '{{WRAPPER}} pre[class*="language-"]' => 'background-color: {{VALUE}};' ),
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
		$settings    = $this->get_settings_for_display();
		$pre_wrapper = 'sh-syntax_wrapper-code ';
		$code_class  = '';
		if ( empty( $settings['sh_syntax_code'] ) ) {
			return;
		}

		// Le language sélectionné
		$language = $settings['sh_syntax_language'];

		// Convertit tous les caractères éligibles en entités HTML
		$syntax_code = htmlentities( $settings['sh_syntax_code'] );

		// Numérotage des lignes
		$line_num = 'yes' === $settings['sh_syntax_linenumbers'] ? 'line-numbers' : '';

		$pre_wrapper .= $settings['sh_syntax_theme'];
		$pre_wrapper .= ' language-' . $language;
		$pre_wrapper .= 'yes' === $settings['sh_syntax_linenumbers'] ? ' line-numbers' : '';

		$code_class .= $settings['sh_syntax_theme'];
		$code_class .= ' language-' . $language;

		$this->add_render_attribute( 'pre_wrapper', 'class', $pre_wrapper );
		$this->add_render_attribute( 'pre_wrapper', 'data-prismjs-copy', esc_html__( 'Copier', 'eac-components' ) );
		$this->add_render_attribute( 'pre_wrapper', 'data-prismjs-copy-error', 'Ctrl+C' );
		$this->add_render_attribute( 'pre_wrapper', 'data-prismjs-copy-success', esc_html__( 'Copié', 'eac-components' ) );
		$this->add_render_attribute( 'pre_wrapper', 'data-prismjs-copy-timeout', 3000 );
		$this->add_render_attribute( 'code_class', 'class', $code_class );

		$pre  = "<div class='eac-syntax_wrapper' dir='ltr'><pre " . $this->get_render_attribute_string( 'pre_wrapper' ) . '>';
		$code = $pre . '<code ' . $this->get_render_attribute_string( 'code_class' ) . '>' . $syntax_code . '</code></pre></div>';

		echo $code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->load_script_code(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * On doit charger le script sous le widget 'code' sinon le preview dans l'éditeur ne s'affiche pas
	 * Normal on n'utilise pas 'content_template' gros malin
	 * Syntaxe Heredoc
	 */
	private function load_script_code() {
		$id  = esc_attr( $this->get_id() );
		$url = esc_url( EAC_PLUGIN_URL . 'assets/js/syntax/prism.js?ver=1.29.0' );
		return <<<EOT
<script>
var eac_core_prism = document.createElement('script');
eac_core_prism.setAttribute('type', 'text/javascript');
eac_core_prism.setAttribute('src', '$url');
eac_core_prism.setAttribute('id', '$id');
document.body.appendChild(eac_core_prism);
</script>
EOT;
	}

	protected function content_template() {}

}
