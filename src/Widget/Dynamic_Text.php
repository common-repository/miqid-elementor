<?php

namespace MIQID\Plugin\Elementor\Widget;

use Elementor\{Controls_Manager, Group_Control_Typography, Repeater};
use DateTime;
use MIQID\Plugin\Core\Util;
use MIQID\Plugin\Elementor\Control\Group\Filter;
use MyCLabs\Enum\Enum;

final class Dynamic_Text extends Base {

	public function get_title() {
		return __( 'Personalized text', 'miqid-elementor' );
	}

	protected function _register_controls() {
		$this->start_controls_section( Util::id( 'Content' ), [
			'label' => $this->get_title(),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Icon' ), [
			'label'   => ( 'Icon' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [ 'value' => 'fas fa-star' ],
		] );

		$this->add_control( Util::id( 'Text' ), [
			'label' => __( 'Text' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$repeater = new Repeater();

		$repeater->add_control( Util::id( 'Icon' ), [
			'label'   => ( 'Icon' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [ 'value' => 'fas fa-star' ],
		] );

		$repeater->add_control( Util::id( 'Text' ), [
			'label' => __( 'Text' ),
			'type'  => Controls_Manager::TEXT,
		] );
		$repeater->add_group_control( Filter::get_type(), [
			'label' => __( 'MIQID Filter' ),
			'name'  => 'miqid',
		] );

		$this->add_control( Util::id( 'Cases' ), [
			'label'  => __( 'Cases' ),
			'type'   => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
		] );

		$this->end_controls_section();


		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Style">
		$this->start_controls_section( \MIQID\Plugin\Elementor\Util::id( 'Style' ), [
			'label' => __( 'Style', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => __( 'Typography', 'miqid-elementor' ),
			'selector' => '{{WRAPPER}} .wrapper ul',
		] );
 
		$this->add_control( Util::id( 'Color' ), [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .wrapper ul' => 'color: {{VALUE}}' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$icon     = $settings[ Util::id( 'Icon' ) ];
		$text     = $settings[ Util::id( 'Text' ) ];
		$cases    = $settings[ Util::id( 'Cases' ) ];

		$cases = current( array_filter( $cases, function ( $case ) {
			$filters = (array) json_decode( $case['miqid_filter'], true );

			$matched_filters = array_filter( $filters, function ( $filter ) {
				if ( $data = $this->get_miqid_property_data( $this->get_miqid_data( $filter['class'] ), $filter['property'] ) ) {
					if ( $data instanceof Enum ) {
						return $data->equals( new $data( absint( $filter['match'] ) ) );
					} else if ( $data instanceof DateTime ) {
						return preg_match( sprintf( '/%s/i', $filter['match'] ), $data->format( 'c' ) );
					}
					return preg_match( sprintf( '/%s/i', $filter['match'] ), $data );
				}

				return false;
			} );

			$diff = array_diff( array_map( 'serialize', $filters ), array_map( 'serialize', $matched_filters ) );

			return empty( $diff );
		} ) );

		$case = [
			'icon' => $icon,
			'text' => $text,
		];

		if ( ! empty( $cases ) && is_array( $cases ) ) {
			$case = array_replace( $case, $cases );
		}

		$item = [];
		if ( ! empty( $case['icon']['value'] ?? false ) ) {
			$item[] = sprintf( '<span class="elementor-icon-list-icon"><i aria-hidden="true" class="%s"></i></span>',
				$case['icon']['value'] );
		}

		$item[] = sprintf( '<span class="elementor-icon-list-text">%s</span>',
			$case['text'] );

		printf( '<div class="wrapper">
    <ul class="elementor-icon-list-items">
        <li class="elementor-icon-list-item">%s</li>
    </ul>
</div>', implode( $item ) );
	}
}