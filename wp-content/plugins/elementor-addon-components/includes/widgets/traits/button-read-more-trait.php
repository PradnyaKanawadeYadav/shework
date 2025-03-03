<?php
namespace EACCustomWidgets\Includes\Widgets\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

trait Button_Read_More_Trait {

	/**
	 * Les contrôles du bouton
	 *
	 * @since 2.2.7 Ajout d'une liste d'arguments pour les conditions d'affichages des controls
	 */
	protected function register_button_more_content_controls( $args = array() ) {
		$default_args = [
			'control_condition' => array(),
		];
		$args = wp_parse_args( $args, $default_args );

		$this->add_control(
			'button_more_label',
			array(
				'label'     => esc_html__( 'Label', 'eac-components' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'ai'        => array( 'active' => false ),
				'default'   => esc_html__( 'En savoir plus', 'eac-components' ),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_add_more_picto',
			array(
				'label'   => esc_html__( 'Ajouter un pictogramme', 'eac-components' ),
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
				'default' => 'no',
				'toggle'  => false,
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_more_picto',
			array(
				'label'              => esc_html__( 'Pictogramme', 'eac-components' ),
				'type'               => Controls_Manager::ICONS,
				'skin'               => 'inline',
				'default'            => array(
					'value'   => 'fas fa-eye',
					'library' => 'fa-solid',
				),
				'frontend_available' => true,
				'condition'          => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ), // Merge les conditions globales et locale
			)
		);

		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';
		$this->add_control(
			'button_more_position',
			array(
				'label'     => esc_html__( 'Position', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'before' => array(
						'title' => is_rtl() ? esc_html__( 'Après', 'eac-components' ) : esc_html__( 'Avant', 'eac-components' ),
						'icon'  => "eicon-h-align-{$start}",
					),
					'after'  => array(
						'title' => is_rtl() ? esc_html__( 'Avant', 'eac-components' ) : esc_html__( 'Après', 'eac-components' ),
						'icon'  => "eicon-h-align-{$end}",
					),
				),
				'default'   => 'before',
				'toggle'    => false,
				'condition' => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ),
			)
		);

		$this->add_control(
			'button_more_marge',
			array(
				'label'              => esc_html__( 'Marges', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => array( 'left', 'right' ),
				'default'            => array(
					'left'     => 0,
					'right'    => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .button__readmore-wrapper .button-icon' => 'margin-block: 0; margin-inline: {{LEFT}}px {{RIGHT}}px;',
				),
				'condition'          => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ),
			)
		);
	}

	/** Les styles du bouton */
	protected function register_button_more_style_controls( $args = array() ) {
		$default_args = [
			'control_condition' => array(),
		];
		$args = wp_parse_args( $args, $default_args );

		$this->start_controls_tabs( 'button_more_controls_tabs' );

			$this->start_controls_tab(
				'button_more_tab_normal',
				array(
					'label'     => esc_html( 'Normal' ),
					'condition' => $args['control_condition'],
				)
			);

				$this->add_control(
					'button_more_color',
					array(
						'label'     => esc_html__( 'Couleur', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper, {{WRAPPER}} .buttons-wrapper' => 'color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'button_more_typo',
						'label'     => esc_html__( 'Typographie', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
						'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_bg',
					array(
						'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper' => 'background-color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'button_more_tab_hover',
				array(
					'label'     => esc_html__( 'Survol', 'eac-components' ),
					'condition' => $args['control_condition'],
				)
			);

				$this->add_control(
					'button_more_color_hover',
					array(
						'label'     => esc_html__( 'Couleur', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'color: {{VALUE}};',
							'{{WRAPPER}} .button__readmore-wrapper:hover svg, {{WRAPPER}} .button__readmore-wrapper:focus svg' => 'fill: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_bg_hover',
					array(
						'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'background-color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_border_color_hover',
					array(
						'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'condition' => array( 'button_more_border_border!' => 'none' ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'border-block-color: {{VALUE}}; border-inline-color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_more_border',
				'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
				'separator' => 'before',
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_more_radius',
			array(
				'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .button__readmore-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_responsive_control(
			'button_more_padding',
			array(
				'label'     => esc_html__( 'Marges internes', 'eac-components' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .button__readmore-wrapper' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_more_shadow',
				'label'     => esc_html__( 'Ombre', 'eac-components' ),
				'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
				'condition' => $args['control_condition'],
			)
		);
	}
}
