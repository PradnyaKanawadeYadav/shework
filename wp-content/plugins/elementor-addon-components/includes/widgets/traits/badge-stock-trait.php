<?php
namespace EACCustomWidgets\Includes\Widgets\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;

trait Badge_Stock_Trait {

	protected function register_stock_content_controls( $args = array() ) {

		$this->add_control(
			'stock_text',
			array(
				'label'       => esc_html__( 'Texte', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'ai'          => array( 'active' => false ),
				'label_block' => false,
				'default'     => esc_html__( 'Rupture de stock', 'eac-components' ),
			)
		);

		$start = is_rtl() ? 'end' : 'start';
		$end   = is_rtl() ? 'start' : 'end';
		$this->add_control(
			'stock_position',
			array(
				'label'        => esc_html__( 'Position', 'eac-components' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'  => array(
						'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
						'icon'  => "eicon-order-{$start}",
					),
					'right' => array(
						'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
						'icon'  => "eicon-order-{$end}",
					),
				),
				'default'      => 'left',
				'prefix_class' => 'badge-stock-pos-',
			)
		);
	}

	protected function register_stock_style_controls( $args = array() ) {

		$this->add_control(
			'stock_color',
			array(
				'label'     => esc_html__( 'Couleur', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'selectors' => array( '{{WRAPPER}} .badge-stock' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'stock_bg',
			array(
				'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
				'selectors' => array( '{{WRAPPER}} .badge-stock' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stock_typo',
				'label'    => esc_html__( 'Typographie', 'eac-components' ),
				'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .badge-stock',
			)
		);

		$this->add_control(
			'stock_width',
			array(
				'label'     => esc_html__( 'Largeur', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 250,
				'step'      => 5,
				'default'   => 90,
				'selectors' => array( '{{WRAPPER}} .badge-stock' => 'inline-size: {{VALUE}}px;' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'stock_height',
			array(
				'label'     => esc_html__( 'Hauteur', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 100,
				'step'      => 5,
				'default'   => 25,
				'selectors' => array( '{{WRAPPER}} .badge-stock' => 'block-size: {{VALUE}}px;' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'stock_border',
				'selector' => '{{WRAPPER}} .badge-stock',
			)
		);

		$this->add_control(
			'stock_radius',
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
					'{{WRAPPER}} .badge-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
	}
}
