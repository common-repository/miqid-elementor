<?php

namespace MIQID\Elementor\Widget;

use Elementor\Controls_Manager;
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Elementor\Util;

final class Display_Text extends Widget_MIQID {

	protected function _register_controls() {
		$this->start_controls_section( 'content', [
			'label' => 'Content',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Deprecated' ), [
			'label'     => __( 'Deprecated use MIQID - Display Widget instead' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'miqid-category', [
			'label'   => __( 'MIQID Category', 'miqid-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'dynamic' => [ 'active' => true ],
			'options' => $this->get_classes_options(),
		] );

		foreach ( $this->get_classes_options() as $key => $text ) {
			$control_name = strtr( $key, [ '\\' => '-' ] );
			$this->add_control( $control_name, [
				'label'       => __( 'MIQID Field', 'miqid-elementor' ),
				'description' => sprintf( __( 'Field available in %s' ), $text ),
				'type'        => Controls_Manager::SELECT,
				'condition'   => [ 'miqid-category' => $key ],
				'dynamic'     => [ 'active' => true ],
				'options'     => $this->get_properties_options( $key ),
			] );
		}

		$this->add_control( 'before', [
			'label' => __( 'Before', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_control( 'after', [
			'label' => __( 'After', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_control( 'element', [
			'label'   => __( 'HTML Tag', 'elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'h1'           => 'H1',
				'h2'           => 'H2',
				'h3'           => 'H3',
				'h4'           => 'H4',
				'h5'           => 'H5',
				'h6'           => 'H6',
				'div'          => 'div',
				'span'         => 'span',
				'p'            => 'p',
				'input_text'   => 'Text Input',
				'input_number' => 'Number Input',
			],
			'default' => 'p',
		] );

		$this->end_controls_section();

		$StyleElements = [ '*', 'label', 'input' ];
		foreach ( $StyleElements as $style_element ) {
			$control_key = strtr( $style_element, [ '*' => 'overall' ] );

			$this->start_controls_section( sprintf( 'style_%s', $control_key ), [
				'label' => __( ucwords( $control_key ), 'miqid-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			] );

			$this->add_control( sprintf( 'margin_%s', $control_key ), [
				'label'      => __( 'Margin', 'miqid-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [ 'unit' => '%' ],
				'selectors'  => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) =>
						'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] );

			$this->add_control( sprintf( 'border_width_%s', $control_key ), [
				'label'      => __( 'Border Width', 'miqid-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 10, ],
					'em' => [ 'min' => 0.1, 'max' => 1, ],
				],
				'default'    => [ 'unit' => 'px', ],
				'selectors'  => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'border-width: {{SIZE}}{{UNIT}};',
				],
			] );

			$this->add_control( sprintf( 'border_style_%s', $control_key ), [
				'label'     => __( 'Border Style', 'miqid-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'none'   => __( 'None', 'miqid-elementor' ),
					'solid'  => __( 'Solid', 'miqid-elementor' ),
					'double' => __( 'Double', 'miqid-elementor' ),
					'dotted' => __( 'Dotted', 'miqid-elementor' ),
				],
				'default'   => 'none',
				'selectors' => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'border-style: {{VALUE}};',
				],
			] );

			$this->add_control( sprintf( 'border_color_%s', $control_key ), [
				'label'     => __( 'Border Color', 'miqid-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'border-color: {{VALUE}}',
				],
			] );

			$this->add_control( sprintf( 'border_radius_%s', $control_key ), [
				'label'      => __( 'Border Radius', 'miqid-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [ 'unit' => '%', ],
				'selectors'  => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] );

			$this->add_control( sprintf( 'padding_%s', $control_key ), [
				'label'      => __( 'Padding', 'miqid-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [ 'unit' => '%', ],
				'selectors'  => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] );

			$this->add_control( sprintf( 'background_color_%s', $control_key ), [
				'label'     => __( 'Background Color', 'miqid-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'background-color: {{VALUE}}',
				],
			] );

			$this->add_control( sprintf( 'color_%s', $control_key ), [
				'label'     => __( 'Color', 'miqid-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					sprintf( '{{WRAPPER}} .elementor-widget-container > %s', $style_element ) => 'color: {{VALUE}}',
				],
			] );

			$this->end_controls_section();
		}
	}

	protected function render() {
		$settings             = $this->get_settings_for_display();
		$miqid_category       = (string) $settings['miqid-category'] ?? '';
		$miqid_category_field = $this->get_miqid_category_field( $miqid_category, $settings );
		$before               = (string) $settings['before'] ?? '';
		$after                = (string) $settings['after'] ?? '';
		$element              = (string) $settings['element'] ?? '';

		$miqid_category_field_arr       = explode( '.', $miqid_category_field );
		$miqid_category_field_arr_class = $miqid_category_field_arr[0] ?? null;
		$miqid_category_field_arr_field = $miqid_category_field_arr[1] ?? null;

		$shortcode       = sprintf( '[miqid-%1$s fields="%2$s"]',
			mb_strtolower( strtr( $miqid_category_field_arr_class, [ '\\' => '-' ] ) ),
			$miqid_category_field_arr_field );
		$shortcode_value = do_shortcode( $shortcode );
		if ( $shortcode_value === $shortcode ) {
			error_log( sprintf( 'Wrong shortcode called: %s', $shortcode ) );
			$shortcode_value = '';
		}

		$element_arr         = explode( '_', $element );
		$element_arr_element = $element_arr[0] ?? null;
		$element_arr_type    = $element_arr[1] ?? null;


		switch ( $element_arr_element ) {
			case 'input':
				$element = implode( array_filter( [
					! empty( $before ) ? '<label for="' . $this->get_id() . '">%2$s</label>' : null,
					'<input id="' . $this->get_id() . '" type="' . $element_arr_type . '" value="%1$s" data-miqid-field="' . esc_attr( $miqid_category_field ) . '"/>',
					! empty( $after ) ? '<label for="' . $this->get_id() . '">%3$s</label>' : null,
				] ) );;
				break;
			default:
				$element = implode( array_filter( [
					sprintf( '<%s data-miqid-field="%s">', $element, esc_attr( $miqid_category_field ) ),
					'%2$s%1$s%3$s',
					sprintf( '</%s>', $element ),
				] ) );
				break;
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf( '<ins>%s</ins>', sprintf( __( 'Bound to: %s' ), $miqid_category_field ) );
		}
		printf( $element, $shortcode_value, $before, $after );
	}
}