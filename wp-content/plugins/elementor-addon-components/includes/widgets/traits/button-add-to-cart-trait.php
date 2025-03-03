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

trait Button_Add_To_Cart_Trait {

	/** Les contrôles du bouton */
	protected function register_button_cart_content_controls( $args = array() ) {

		$this->add_control(
			'button_cart_label',
			array(
				'label'   => esc_html__( 'Label', 'eac-components' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'ai'      => array( 'active' => false ),
				'default' => esc_html__( 'Ajouter au panier', 'eac-components' ),
			)
		);

		$this->add_control(
			'button_add_cart_picto',
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
			)
		);

		$this->add_control(
			'button_cart_picto',
			array(
				'label'     => esc_html__( 'Pictogramme', 'eac-components' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'default'   => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				),
				'condition' => array( 'button_add_cart_picto' => 'yes' ),
			)
		);

		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';
		$this->add_control(
			'button_cart_position',
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
				'condition' => array( 'button_add_cart_picto' => 'yes' ),
			)
		);

		$this->add_control(
			'button_cart_marges',
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
					'{{WRAPPER}} .button__cart-wrapper .button-icon' => 'margin-block: 0; margin-inline: {{LEFT}}px {{RIGHT}}px;',
				),
				'condition'          => array( 'button_add_cart_picto' => 'yes' ),
			)
		);
	}

	/** Les styles du bouton */
	protected function register_button_cart_style_controls( $args = array() ) {

		$this->start_controls_tabs( 'button_cart_controls_tabs' );

			$this->start_controls_tab(
				'button_cart_tab_normal',
				array(
					'label' => esc_html( 'Normal' ),
				)
			);
				$this->add_control(
					'button_cart_color',
					array(
						'label'     => esc_html__( 'Couleur', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__cart-wrapper, {{WRAPPER}} .buttons-wrapper' => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'     => 'button_cart_typo',
						'label'    => esc_html__( 'Typographie', 'eac-components' ),
						'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
						'selector' => '{{WRAPPER}} .button__cart-wrapper',
					)
				);

				$this->add_control(
					'button_cart_bg',
					array(
						'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array( '{{WRAPPER}} .button__cart-wrapper' => 'background-color: {{VALUE}};' ),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'button_cart_tab_hover',
				array(
					'label' => esc_html__( 'Survol', 'eac-components' ),
				)
			);

				$this->add_control(
					'button_cart_color_hover',
					array(
						'label'     => esc_html__( 'Couleur', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__cart-wrapper:hover, {{WRAPPER}} .button__cart-wrapper:focus' => 'color: {{VALUE}}',
							'{{WRAPPER}} .button__cart-wrapper:hover svg, {{WRAPPER}} .button__cart-wrapper:focus svg' => 'fill: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'button_cart_bg_hover',
					array(
						'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__cart-wrapper:hover, {{WRAPPER}} .button__cart-wrapper:focus' => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					'button_cart_border_color_hover',
					array(
						'label'     => esc_html__( 'Couleur de la bordure', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'condition' => array( 'button_cart_border_border!' => 'none' ),
						'selectors' => array(
							'{{WRAPPER}} .button__cart-wrapper:hover, {{WRAPPER}} .button__cart-wrapper:focus' => 'border-block-color: {{VALUE}}; border-inline-color: {{VALUE}};',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_cart_border',
				'selector'  => '{{WRAPPER}} .button__cart-wrapper',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_cart_radius',
			array(
				'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .button__cart-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_cart_padding',
			array(
				'label'     => esc_html__( 'Marges internes', 'eac-components' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .button__cart-wrapper' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_cart_shadow',
				'label'    => esc_html__( 'Ombre', 'eac-components' ),
				'selector' => '{{WRAPPER}} .button__cart-wrapper',
			)
		);
	}
}
