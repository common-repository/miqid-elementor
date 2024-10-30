<?php

namespace MIQID\Plugin\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use MIQID\Plugin\Elementor\Util;

final class Display_Widget extends Base {

	public function get_title() {
		return __( 'Personalized information field', 'miqid-elementor' );
	}

	protected function _register_controls() {
		// <editor-fold desc="Content">
		$this->start_controls_section( Util::id( 'Content' ), [
			'label' => $this->get_title(),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Element' ), [
			'label'   => __( 'HTML Tag', 'elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'h1'    => 'H1',
				'h2'    => 'H2',
				'h3'    => 'H3',
				'h4'    => 'H4',
				'h5'    => 'H5',
				'h6'    => 'H6',
				'div'   => 'div',
				'span'  => 'span',
				'p'     => 'p',
				'input' => 'Input',
			],
			'default' => 'p',
		] );

		$this->add_control( Util::id( 'Element', 'Type' ), [
			'label'     => __( 'Input Type', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'text'   => _x( 'Text', 'Input Type', 'miqid-elementor' ),
				'number' => _x( 'Number', 'Input Typ', 'miqid-elementor' ),
			],
			'condition' => [ Util::id( 'Element' ) => 'input' ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( Util::id( 'Class' ), [
			'label'   => __( 'MIQID Category', 'miqid-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'dynamic' => [ 'active' => true ],
			'options' => $this->get_classes_options(),
		] );

		foreach ( $this->get_classes_options() as $key => $text ) {
			$repeater->add_control( Util::id( strtr( $key, [ '\\' => '-' ] ) ), [
				'label'       => __( 'MIQID Field', 'miqid-elementor' ),
				'description' => sprintf( __( 'Field available in %s' ), $text ),
				'type'        => Controls_Manager::SELECT,
				'condition'   => [ Util::id( 'Class' ) => $key ],
				'dynamic'     => [ 'active' => true ],
				'options'     => $this->get_properties_options( $key ),
			] );
		}

		$repeater->add_control( util::id( 'Before' ), [
			'label' => __( 'Before', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$repeater->add_control( util::id( 'After' ), [
			'label' => __( 'After', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_control( Util::id( 'Repeater' ), [
			'label'  => __( 'MIQID Fields' ),
			'type'   => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Style">
		$this->start_controls_section( Util::id( 'Style' ), [
			'label' => __( 'Style', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => __( 'Typography', 'miqid-elementor' ),
			'selector' => '{{WRAPPER}} .wrapper',
		] );

		$this->add_control( Util::id( 'Color' ), [
			'label'     => __( 'Color' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .wrapper' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => __( 'Border', 'miqid-elementor' ),
			'selector' => '{{WRAPPER}} .wrapper',
		] );

		$this->add_control( Util::id( 'Border', 'Radius' ), [
			'label'      => __( 'Border Radius' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [ '{{WRAPPER}} .wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => __( 'Background', 'miqid-elementor' ),
			'selector' => '{{WRAPPER}} .wrapper',
		] );

		$this->add_control( Util::id( 'Padding' ), [
			'label'      => __( 'Padding' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [ '{{WRAPPER}} .wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
		// </editor-fold>
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$element      = $settings[ Util::id( 'Element' ) ];
		$element_type = $settings[ Util::id( 'Element', 'Type' ) ];
		$repeater     = $settings[ Util::id( 'Repeater' ) ];

		$bound  = [];
		$output = [];
		foreach ( $repeater as $item ) {
			$class    = $item[ Util::id( 'Class' ) ];
			$property = $item[ Util::id( strtr( $class, [ '\\' => '-' ] ) ) ];
			$before   = $item[ Util::id( 'Before' ) ];
			$after    = $item[ Util::id( 'After' ) ];

			$shortcode = sprintf( '[%1$s fields="%2$s"]',
				mb_strtolower( strtr( $class, [ 'Plugin\\Core\\Classes\\DTO\\' => '', '\\' => '-', ] ) ),
				$property );

			$bound[] = sprintf( '%s.%s', strtr( $class, [ 'MIQID\\Plugin\\Core\\Classes\\DTO\\' => '', '\\' => '-', ] ), $property );

			if ( ( $shortcode_value = do_shortcode( $shortcode ) ) && $shortcode_value === $shortcode ) {
				$shortcode_value = null;
			}

			if ( ! empty( $shortcode_value ) ) {
				$output[] = implode( array_filter( [
					$before,
					trim( $shortcode_value ),
					$after,
				] ) );
			}
		}

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			printf( '<ins>Bound To:<br /><ul><li>%s</li></ul></ins>', implode( '</li><li>', $bound ) );
		}

		$this->add_render_attribute( 'element', 'class', 'wrapper' );

		if ( $element == 'input' ) {
			$this->add_render_attribute( 'element', 'type', $element_type );
			$this->add_render_attribute( 'element', 'value', implode( array_filter( $output ) ) );
		}

		if ( $element == 'input' ) {
			printf( '<%1$s %2$s />',
				$element,
				$this->get_render_attribute_string( 'element' ) );
		} else {
			printf( '<%1$s %3$s>%2$s</%1$s>',
				$element,
				implode( array_filter( $output ) ),
				$this->get_render_attribute_string( 'element' ) );
		}
	}
}