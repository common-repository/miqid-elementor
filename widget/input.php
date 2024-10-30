<?php

namespace MIQID\Elementor\Widget;

use Elementor\{Controls_Manager, Core\Kits\Documents\Tabs\Global_Typography, Group_Control_Typography};
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Classes\API\Business\Profile;
use MIQID\Plugin\Core\Classes\DTO\DriversLicense;
use MIQID\Plugin\Core\Classes\DTO\HealthInsuranceCard;
use MIQID\Plugin\Core\Classes\DTO\HttpResponse;
use MIQID\Plugin\Core\Classes\DTO\Passport;
use MIQID\Plugin\Elementor\Util;

final class Input extends Widget_MIQID {
	private $CSS;
	private $JS;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->CSS = $this->_register_css(
			'verified.css' );

	}

	public function get_style_depends() {
		return $this->CSS;
	}

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

		$this->add_control( 'type', [
			'label'   => __( 'Type', 'miqid-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'text'     => __( 'Text', 'miqid-elementor' ),
				'password' => __( 'password', 'miqid-elementor' ),
				'hidden'   => __( 'Hidden', 'miqid-elementor' ),
				'email'    => __( 'Email', 'miqid-elementor' ),
				'number'   => __( 'number', 'miqid-elementor' ),
				'tel'      => __( 'tel', 'miqid-elementor' ),
				'url'      => __( 'url', 'miqid-elementor' ),

				'date'           => __( 'Date', 'miqid-elementor' ),
				'time'           => __( 'time', 'miqid-elementor' ),
				'datetime-local' => __( 'DateTime-Local', 'miqid-elementor' ),
				'month'          => __( 'month', 'miqid-elementor' ),
				'week'           => __( 'week', 'miqid-elementor' ),

				'checkbox' => __( 'Checkbox', 'miqid-elementor' ),
				'radio'    => __( 'radio', 'miqid-elementor' ),

				'color'  => __( 'Color', 'miqid-elementor' ),
				'file'   => __( 'File', 'miqid-elementor' ),
				'image'  => __( 'Image', 'miqid-elementor' ),
				'range'  => __( 'range', 'miqid-elementor' ),
				'reset'  => __( 'reset', 'miqid-elementor' ),
				'search' => __( 'search', 'miqid-elementor' ),
				'submit' => __( 'submit', 'miqid-elementor' ),
			],
		] );

		$this->add_control( 'label', [
			'label' => __( 'Label', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_control( 'placeholder', [
			'label' => __( 'Placeholder', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_control( 'required', [
			'label' => __( 'Required', 'miqid-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );

		$this->end_controls_section();

		// <editor-fold desc="Style">
		$this->start_controls_section( 'layout', [
			'label' => __( 'Layout', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'direction', [
			'label'   => __( 'Direction', 'miqid-elementor' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => [
				'horizontal' => [
					'title' => __( 'Horizontal' ),
					'icon'  => 'fas fa-grip-lines',
				],
				'vertical'   => [
					'title' => __( 'Vertical' ),
					'icon'  => 'fas fa-grip-lines-vertical',
				],
			],
			'default' => 'vertical',
		] );

		$this->end_controls_section();
		// </editor-fold>

		// <editor-fold desc="style_label">
		$this->start_controls_section( 'style_label', [
			'label' => __( 'Label', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'label_color', [
			'label'     => __( 'Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} label' => 'color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'typography',
			'global'   => [
				'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
			],
			'selector' => '{{WRAPPER}} label',
		] );

		$this->add_control( 'label_font_weight', [
			'label'     => __( 'Font-Weight', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => $this->get_font_weights(),
			'default'   => '500',
			'selectors' => [
				'{{WRAPPER}} label' => 'font-weight: {{VALUE}}',
			],
		] );

		$this->add_control( 'label_padding', [
			'label'      => __( 'Padding', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'label_width', [
			'label'      => __( 'Padding', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 120,
				'unit' => 'px',
			],
			'size_units' => [ 'px', '%', 'em' ],
			'range'      => [
				'px' => [
					'min' => 10,
					'max' => 600,
				],
				'em' => [
					'min' => 0.1,
					'max' => 20,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} label' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
		// </editor-fold>

		// <editor-fold desc="style_input">
		$this->start_controls_section( 'style_input', [
			'label' => __( 'Input', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'input_padding', [
			'label'      => __( 'Padding', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control( 'input_border_color', [
			'label'     => __( 'Border Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} input' => 'border-color: {{VALUE}}',
			],
		] );

		$this->add_control( 'input_border_width', [
			'label'      => __( 'Border Width', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 1,
				'unit' => 'px',
			],
			'size_units' => [ 'px', 'em' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 10,
				],
				'em' => [
					'min' => 0.1,
					'max' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} input' => 'border-width: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_control( 'input_border_style', [
			'label'     => __( 'Border Width', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'solid'  => __( 'Solid', 'miqid-elementor' ),
				'double' => __( 'Double', 'miqid-elementor' ),
				'dotted' => __( 'Dotted', 'miqid-elementor' ),
			],
			'selectors' => [
				'{{WRAPPER}} input' => 'border-style: {{VALUE}};',
			],
		] );

		$this->add_control( 'input_border_radius', [
			'label'      => __( 'Border Radius', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );


		$this->end_controls_section();
		// </editor-fold>
	}

	protected function render() {
		global $inputs;

		if ( ! is_array( $inputs ) ) {
			$inputs = [];
		}

		$settings             = $this->get_settings_for_display();
		$miqid_category       = (string) ( $settings['miqid-category'] ?? '' );
		$miqid_category_field = $this->get_miqid_category_field( $miqid_category, $settings );
		$type                 = (string) ( $settings['type'] ?? '' );
		$label                = (string) ( $settings['label'] ?? '' );
		$placeholder          = (string) ( $settings['placeholder'] ?? '' );
		$required             = (string) ( $settings['required'] ?? '' );
		$direction            = (string) ( $settings['direction'] ?? '' );

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

		$this->add_render_attribute( 'wrapper', 'class', 'wrapper' );
		$this->add_render_attribute( 'input', 'data-miqid-field', $miqid_category_field );
		if ( ! empty( $direction ) ) {
			$this->add_render_attribute( 'wrapper', 'class', $direction );
		}
		if ( ! empty( $shortcode_value ) )
			switch ( $miqid_category ) {
				case DriversLicense::class:
				case \MIQID\Plugin\Core\Classes\DTO\Business\DriversLicense::class:
				case Passport::class:
				case \MIQID\Plugin\Core\Classes\DTO\Business\Passport::class:
				case HealthInsuranceCard::class:
				case \MIQID\Plugin\Core\Classes\DTO\Business\HealthInsuranceCard::class:
					$this->add_render_attribute( 'wrapper', 'class', 'miqid-verified' );
					break;
			}

		$this->add_render_attribute( 'input', 'type', $type );
		$this->add_render_attribute( 'input', 'name', sprintf( '[%s]', implode( '][', $miqid_category_field_arr ) ) );
		$this->add_render_attribute( 'input', 'value', $shortcode_value );
		if ( ! empty( $placeholder ) ) {
			$this->add_render_attribute( 'input', 'placeholder', $placeholder );
		}
		if ( filter_var( $required, FILTER_VALIDATE_BOOLEAN ) ) {
			$this->add_render_attribute( 'input', 'required', 'required' );
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf( '<ins>%s</ins>', sprintf( __( 'Bound to: %s' ), $miqid_category_field ) );
		}

		printf( '<div %s>', $this->get_render_attribute_string( 'wrapper' ) );
		printf( '<label %2$s>%1$s</label>', $label, $this->get_render_attribute_string( 'label' ) );
		printf( '<input %1$s>', $this->get_render_attribute_string( 'input' ) );
		printf( '</div>' );
	}
}