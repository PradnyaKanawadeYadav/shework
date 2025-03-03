<?php
/**
 * Class: Lecteur_Audio_Widget
 * Name: Flux webradio
 * Slug: eac-addon-lecteur-audio
 *
 * Description: Lecteur_Audio_Widget affiche une liste de web-radios
 * qui peuvent être écoutés
 *
 * @since 1.0.0
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
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Lecteur_Audio_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Lecteur_Audio_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'eac-player', EAC_Plugin::instance()->get_script_url( 'assets/js/audioplayer/player' ), array( 'jquery' ), '1.0.0', true );
		EAC_Plugin::instance()->register_script( 'eac-webradio-player', 'assets/js/elementor/eac-webradio-player', array( 'jquery', 'elementor-frontend', 'eac-player' ), '1.0.0',
			array(
				'strategy' => 'defer',
				'in_footer' => true,
			)
		);

		wp_register_style( 'eac-webradio-player', EAC_Plugin::instance()->get_style_url( 'assets/css/webradio-player' ), array( 'eac-frontend' ), '1.0.0' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'lecteur-audio';

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
		return array( 'eac-player', 'eac-webradio-player' );
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
		return array( 'eac-webradio-player' );
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
			'la_audio_settings',
			array(
				'label' => esc_html__( 'Liste des flux audio', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'la_unique_instance',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_info',
					'raw'             => __( "Atlas des flux audio des radios de langue Française - <a href='http://fluxradios.blogspot.com/' target='_blank' rel='nofolow noopener noreferrer'>Consulter ce site</a>", 'eac-components' ),
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'la_item_title',
				array(
					'label' => esc_html__( 'Titre', 'eac-components' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$repeater->add_control(
				'la_item_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com/xxxx.mp3/',
				)
			);

			$this->add_control(
				'la_radio_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'la_item_title' => 'France - France Inter',
							'la_item_url'   => array( 'url' => 'https://direct.franceinter.fr/live/franceinter-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP',
							'la_item_url'   => array( 'url' => 'https://direct.fipradio.fr/live/fip-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP Rock',
							'la_item_url'   => array( 'url' => 'https://direct.fipradio.fr/live/fip-webradio1.mp3' ),
						),
						array(
							'la_item_title' => 'France - FIP Tout nouveau',
							'la_item_url'   => array( 'url' => 'https://direct.fipradio.fr/live/fip-webradio5.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Culture',
							'la_item_url'   => array( 'url' => 'https://direct.franceculture.fr/live/franceculture-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Info',
							'la_item_url'   => array( 'url' => 'https://direct.franceinfo.fr/live/franceinfo-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Musique',
							'la_item_url'   => array( 'url' => 'https://direct.francemusique.fr/live/francemusique-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - RFI Monde',
							'la_item_url'   => array( 'url' => 'https://live02.rfi.fr/rfimonde-96k.mp3' ),
						),
						array(
							'la_item_title' => 'France - RFI Afrique',
							'la_item_url'   => array( 'url' => 'https://live02.rfi.fr/rfiafrique-64.mp3' ),
						),
						array(
							'la_item_title' => 'France - France Musique',
							'la_item_url'   => array( 'url' => 'https://direct.francemusique.fr/live/francemusique-midfi.mp3' ),
						),
						array(
							'la_item_title' => 'France - Radio Classique',
							'la_item_url'   => array( 'url' => 'https://radioclassique.ice.infomaniak.ch/radioclassique-high.mp3' ),
						),
						array(
							'la_item_title' => 'Canada - Montréal',
							'la_item_url'   => array( 'url' => 'https://newcap.leanstream.co/CHBMFM' ),
						),
						array(
							'la_item_title' => 'Suisse - RTS La Première',
							'la_item_url'   => array( 'url' => 'https://stream.srg-ssr.ch/m/la-1ere/mp3_128' ),
						),
						array(
							'la_item_title' => 'Italia - RAI UNO',
							'la_item_url'   => array( 'url' => 'https://icestreaming.rai.it/1.mp3' ),
						),
						array(
							'la_item_title' => 'Radio Caroline - Flashback',
							'la_item_url'   => array( 'url' => 'https://sc2.radiocaroline.net:10558/;.mp3' ),
						),
					),
					'title_field' => '{{{ la_item_title }}}',
					'button_text' => esc_html__( 'Ajouter un flux', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'la_general_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'la_wrapper_width',
				array(
					'label'       => esc_html__( 'Largeur', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( '%', 'vw' ),
					'default'     => array(
						'size' => 50,
						'unit' => '%',
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'size' => 100,
						'unit' => '%',
					),
					'range'       => array(
						'%'  => array(
							'max'  => 100,
							'step' => 10,
						),
						'vw' => array(
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .eac-lecteur-audio' => 'inline-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'la_wrapper_alignment_h',
				array(
					'label'     => esc_html__( 'Alignement horizontal', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'    => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'selectors_dictionary' => array(
						'start'  => '0 auto',
						'center' => 'auto',
						'end'    => 'auto 0',
					),
					'selectors' => array( '{{WRAPPER}} .eac-lecteur-audio' => 'margin-inline: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'la_list_options_typo',
					'label'     => esc_html__( 'Typographie de la liste', 'eac-components' ),
					'selector'  => '.la-options-items-list .select__options-items',
				)
			);

			$this->add_control(
				'la_wrapper_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .eac-lecteur-audio' => 'background-color: {{VALUE}};' ),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'la_wrapper_border',
					'selector' => '{{WRAPPER}} .eac-lecteur-audio',
				)
			);

			$this->add_control(
				'la_wrapper_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .eac-lecteur-audio' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'la_wrapper_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .eac-lecteur-audio',
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
		if ( empty( $settings['la_radio_list'] ) ) {
			return;
		}
		?>
		<div class='eac-lecteur-audio'>
			<?php $this->render_player(); ?>
			<div class='la-lecteur-audio'>
				<?php if ( isset( $settings['la_radio_list'][0]['la_item_url']['url'] ) && $this->is_valid_url( $settings['la_radio_list'][0]['la_item_url']['url'] ) ) { ?>
					<audio class='listen' preload='none' data-size='150' src="<?php echo esc_url( $settings['la_radio_list'][0]['la_item_url']['url'] ); ?>" aria-labelledby="listbox_<?php echo esc_attr( $this->get_id() ); ?>"></audio>
				<?php } ?>
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
	protected function render_player() {
		$settings = $this->get_settings_for_display();
		?>
		<div class='la-select-item-list'>
			<div class='la-options-items-list'>
			<label id="label_<?php echo esc_attr( $this->get_id() ); ?>" class='visually-hidden' for="listbox_<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Liste des flux audio', 'eac-components' ); ?></label>
				<select id="listbox_<?php echo esc_attr( $this->get_id() ); ?>" class='select__options-items' aria-labelledby="label_<?php echo esc_attr( $this->get_id() ); ?>">;
					<?php foreach ( $settings['la_radio_list'] as $item ) {
						if ( ! empty( $item['la_item_url']['url'] ) && $this->is_valid_url( $item['la_item_url']['url'] ) ) : ?>
							<option value="<?php echo esc_url( $item['la_item_url']['url'] ); ?>"><?php echo esc_html( $item['la_item_title'] ); ?></option>
						<?php endif;
					} ?>
				</select>
			</div>
		</div>
		<?php
	}

	/**
	 * is_valid_url
	 *
	 * @param mixed $url
	 *
	 * @return bool
	 */
	private function is_valid_url( $url ): bool {
		return ! preg_match( '/\bjavascript\b/i', $url ) && filter_var( $url, FILTER_VALIDATE_URL );
	}

	protected function content_template() {}
}
