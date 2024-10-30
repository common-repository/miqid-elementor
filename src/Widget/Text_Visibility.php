<?php

namespace MIQID\Plugin\Elementor\Widget;

use DateTime;
use Elementor\Controls_Manager;
use MIQID\Plugin\Core\Classes\DTO\HttpResponse;
use MIQID\Plugin\Elementor\Control\Group\Filter;
use MIQID\Plugin\Elementor\Util;
use MyCLabs\Enum\Enum;

class Text_Visibility extends Base {

	protected function _register_controls() {
		$this->start_controls_section( Util::id( 'Content' ), [
			'label' => __( 'Text Visibility' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Text' ), [
			'label' => 'Text',
			'type'  => Controls_Manager::WYSIWYG,
		] );

		$this->add_group_control( Filter::get_type(), [
			'label' => __( 'MIQID Filter' ),
			'name'  => 'miqid',
		] );

		$this->add_control( 'negate', [
			'label' => __( 'Visible' ),
			'type'  => Controls_Manager::SWITCHER,
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$text     = $settings[ Util::id( 'Text' ) ];
		$negate   = $settings[ Util::id( 'Negate' ) ];
		$filters  = (array) json_decode( $settings['miqid_filter'], true );

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

		$is_match = empty( $diff );

		if ( ( $negate && $is_match ) || ( ! $negate && ! $is_match ) ) {
			printf( '<div class="wrapper">%s</div>', $text );
		}
	}
}