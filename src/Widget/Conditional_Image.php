<?php

namespace MIQID\Plugin\Elementor\Widget;

use DateTime;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use MIQID\Plugin\Elementor\Control\Group\Filter;
use MIQID\Plugin\Elementor\Util;
use MyCLabs\Enum\Enum;

final class Conditional_Image extends Base {

	public function get_icon() {
		return 'eicon-image';
	}

	protected function _register_controls() {
		$this->start_controls_section( Util::id( 'content', 'image', 'section' ), [
			'tab'   => Controls_Manager::TAB_CONTENT,
			'label' => __( 'Conditional Image', 'miqid-elementor' ),
		] );

		$this->add_control( Util::id( 'content', 'image' ), [
			'type'  => Controls_Manager::MEDIA,
			'label' => __( 'Image' ),
		] );

		$this->add_responsive_control( Util::id( 'content', 'image', 'align' ), [
			'label'        => __( 'Alignment', 'elementor' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => [
				'left'    => [ 'title' => __( 'Left', 'elementor' ), 'icon' => 'eicon-text-align-left', ],
				'center'  => [ 'title' => __( 'Center', 'elementor' ), 'icon' => 'eicon-text-align-center', ],
				'right'   => [ 'title' => __( 'Right', 'elementor' ), 'icon' => 'eicon-text-align-right', ],
				'justify' => [ 'title' => __( 'Justified', 'elementor' ), 'icon' => 'eicon-text-align-justify', ],
			],
			'prefix_class' => 'elementor%s-align-',
			'default'      => '',
		] );

		$this->add_group_control( Filter::get_type(), [
			'label' => __( 'MIQID Filter' ),
			'name'  => 'miqid',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image    = $settings[ Util::id( 'content', 'image' ) ] ?? '';
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

		if ( empty($diff) ) {
			printf( '<picture class="attachment-large size-large"><img src="%1$s"/></picture>',
				$image['url'] );
		}
	}
}