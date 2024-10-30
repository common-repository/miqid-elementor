<?php

namespace MIQID\Elementor\Widget;

use Elementor\{Controls_Manager, Icons_Manager, Repeater};
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Util;

final class Dynamic_Text extends Widget_MIQID {


	protected function _register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => __( 'Content', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Deprecated' ), [
			'label'     => __( 'Deprecated, replaced by MIQID - Dynamic Text - V2' ),
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

		$this->add_control( 'default_text', [
			'label'   => 'Default text',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => true ],
		] );

		$this->add_control( 'text_color', [
			'label' => 'Text Color',
			'type'  => Controls_Manager::COLOR,
		] );

		$this->add_control( 'font_family', [
			'label' => 'Typography',
			'type'  => Controls_Manager::FONT,
		] );

		$this->add_control( 'icon', [
			'label'   => ( 'Icon' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [ 'value' => 'fas fa-star' ],
		] );

		$this->add_control( 'icon_color', [
			'label' => 'Icon Color',
			'type'  => Controls_Manager::COLOR,
		] );


		// <editor-fold desc="Repeater">
		$repeater = new Repeater();

		$repeater->add_control( 'match', [
			'label'   => 'Text Match',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => true ],
		] );

		$repeater->add_control( 'text', [
			'label'   => 'Display text',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => true ],
		] );

		$repeater->add_control( 'text_color', [
			'label' => 'Text Color',
			'type'  => Controls_Manager::COLOR,
		] );

		$repeater->add_control( 'font_family', [
			'label' => 'Typography',
			'type'  => Controls_Manager::FONT,
		] );

		$repeater->add_control( 'icon_color', [
			'label' => 'Icon Color',
			'type'  => Controls_Manager::COLOR,
		] );

		$this->add_control( 'switch', [
			'label'         => 'Switch cases',
			'type'          => Controls_Manager::REPEATER,
			'default'       => [
				[ 'text' => 'Case #1', ],
				[ 'text' => 'Case #2', ],
				[ 'text' => 'Case #3', ],
			],
			'prevent_empty' => true,
			'fields'        => $repeater->get_controls(),
			'title_field'   => '{{{ text }}}',
		] );
		// </editor-fold>

		$this->end_controls_section();

		$this->start_controls_section( 'icon_style',
			[
				'label' => 'Icon',
				'tab'   => Controls_Manager::TAB_STYLE,
			] );

		$this->add_control( 'icon_size', [
			'label'      => 'Icon size',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 14,
			],
			'selectors'  => [
				'{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings             = $this->get_settings_for_display();
		$miqid_category       = (string) $settings['miqid-category'] ?? '';
		$miqid_category_field = $this->get_miqid_category_field( $miqid_category, $settings );
		$default_text         = (string) $settings['default_text'] ?? '';
		$text_color           = $settings['text_color'];
		$font_family          = $settings['font_family'];
		$icon                 = $settings["icon"];
		$icon_color           = $settings["icon_color"];
		$switch               = $settings["switch"];

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

		$case = [
			'text'        => $default_text,
			'text_color'  => $text_color,
			'font_family' => $font_family,
			'icon_color'  => $icon_color,
			'icon'        => $icon,
		];

		$switch = array_filter( $switch, function ( $case ) use ( $shortcode_value ) {
			return preg_match( sprintf( '/%s/i', $case['match'] ), $shortcode_value );
		} );

		if ( is_array( $switch ) && ! empty( $switch ) ) {
			$switch = array_filter( current( $switch ) );
		}

		$case = array_replace( $case, $switch );

		$content = '';
		if ( ! empty( $icon['value'] ) ) {
			$content = sprintf( '<span class="elementor-icon-list-icon"><i aria-hidden="true" class="%s" style="%s"></i></span>',
				$case['icon']['value'],
				implode( ';', array_filter( [
					$case['icon_color'] ? sprintf( 'color: %s', $case['icon_color'] ) : null,
				] ) )
			);
		}

		$content = sprintf( '%1$s<span class="elementor-icon-list-text" style="%3$s">%2$s</span>',
			$content,
			$case['text'],
			implode( '; ', array_filter( [
				$case['text_color'] ? sprintf( 'color: %s', $case['text_color'] ) : null,
				$case['font_family'] ? sprintf( 'font-family: %s', $case['font_family'] ) : null,
			] ) )
		);

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf( '<ins>%s</ins>', sprintf( __( 'Bound to: %s' ), $miqid_category_field ) );
		}

		printf( '<ul class="elementor-icon-list-items">
    <li class="elementor-icon-list-item">
    	%1$s
    </li>
</ul>',
			$content
		);
		/*
		$cases    = $settings['switch'];
		$match    = do_shortcode( sprintf( '[miqid-%s fields="%s"]', mb_strtolower( array_shift( $miqid ) ), array_shift( $miqid ) ) );
		$cases    = current( array_filter( $cases, function ( $case ) use ( $match ) {
			return preg_match( '/' . $case['match'] . '/i', $match );
		} ) );

		if ( empty( $cases ) ) {
			$cases = [
				'text'        => $settings['default_text'],
				'text_color'  => $settings['text_color'],
				'font_family' => $settings['font_family'],
				'icon_color'  => $settings['icon_color'],
			];
		}



		printf( $content );*/
	}
}