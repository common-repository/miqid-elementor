<?php

namespace MIQID\Plugin\Elementor\Widget;

use Elementor\{Controls_Manager, Repeater, Utils};
use MIQID\Plugin\Core\Util;
use MIQID\Plugin\Elementor\Control\Group\Filter;
use MyCLabs\Enum\Enum;

final class Dynamic_Images extends Base {

	public function get_title() {
		return __( 'Personalized image', 'miqid-elementor' );
	}

	protected function _register_controls() {

		$this->start_controls_section( Util::id( 'Content' ), [
			'label' => $this->get_title(),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Image' ), [
			'label' => __( 'Default Image', 'miqid-elementor' ),
			'type'  => Controls_Manager::MEDIA,
		] );

		$this->add_control( Util::id( 'Align' ), [
			'label'        => __( 'Alignment', 'elementor' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => [
				'left'    => [ 'title' => __( 'Left', 'elementor' ), 'icon' => 'eicon-text-align-left' ],
				'center'  => [ 'title' => __( 'Center', 'elementor' ), 'icon' => 'eicon-text-align-center' ],
				'right'   => [ 'title' => __( 'Right', 'elementor' ), 'icon' => 'eicon-text-align-right' ],
				'justify' => [ 'title' => __( 'Justified', 'elementor' ), 'icon' => 'eicon-text-align-justify' ],
			],
			'prefix_class' => 'elementor-align-',
			'default'      => '',
		] );

		$this->add_control( Util::id( 'Click' ), [
			'label'   => 'Click action',
			'type'    => Controls_Manager::SELECT,
			'options' => [ 'none' => "None", 'link' => 'Link', 'popup' => 'Popup' ],
			'default' => 'none',
		] );

		$this->add_control( 'popupID', [
			'label'     => 'Post ID of popup',
			'condition' => [ Util::id( 'Click' ) => 'popup' ],
			'type'      => Controls_Manager::TEXT,
			'dynamic'   => [ 'active' => true ],
		] );

		$this->add_control( 'link', [
			'label'     => 'Link',
			'condition' => [ Util::id( 'Click' ) => 'link' ],
			'type'      => Controls_Manager::URL,
			'dynamic'   => [ 'active' => true ],
		] );

		// <editor-fold desc="Repeater">
		$repeater = new Repeater();

		$repeater->add_control( Util::id( 'Image' ), [
			'label'   => 'Display image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Utils::get_placeholder_image_src() ],
		] );

		$repeater->add_control( Util::id( 'Click' ), [
			'label'   => 'Click action',
			'type'    => Controls_Manager::SELECT,
			'options' => [ 'none' => "None", 'link' => 'Link', 'popup' => 'Popup' ],
			'default' => 'none',
		] );

		$repeater->add_control( 'popupID', [
			'label'     => 'Post ID of popup',
			'condition' => [ Util::id( 'Click' ) => 'popup' ],
			'type'      => Controls_Manager::TEXT,
			'dynamic'   => [ 'active' => true ],
		] );

		$repeater->add_control( 'link', [
			'label'     => 'Link',
			'condition' => [ Util::id( 'Click' ) => 'link' ],
			'type'      => Controls_Manager::URL,
			'dynamic'   => [ 'active' => true ],
		] );

		$repeater->add_group_control( Filter::get_type(), [
			'label' => __( 'MIQID Filter' ),
			'name'  => 'miqid',
		] );

		$this->add_control( Util::id( 'Switch' ), [
			'label'  => __( 'Switch Cases' ),
			'type'   => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
		] );
		// </editor-fold>
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image    = $settings[ Util::id( 'Image' ) ] ?? $settings['default_image'] ?? '';
		$click    = $settings[ Util::id( 'Click' ) ] ?? $settings['clickAction'];
		$popupID  = $settings[ Util::id( 'popupID' ) ] ?? $settings['popupID'];
		$link     = $settings[ Util::id( 'link' ) ] ?? $settings['link'];
		$switch   = $settings[ Util::id( 'Switch' ) ] ?? $settings['switch'];

		$switch = current( array_filter( $switch, function ( $switch ) {
			$filters = (array) json_decode( $switch['miqid_filter'], true );

			$matched_filters = array_filter( $filters, function ( $filter ) {
				if ( $data = $this->get_miqid_property_data( $this->get_miqid_data( $filter['class'] ), $filter['property'] ) ) {

					if ( $data instanceof Enum ) {
						return $data->equals( new $data( absint( $filter['match'] ) ) );
					}

					return preg_match( sprintf( '/%s/i', $filter['match'] ), $data );
				}

				return false;
			} );

			$diff = array_diff( array_map( 'serialize', $filters ), array_map( 'serialize', $matched_filters ) );

			return empty( $diff );
		} ) );

		$case = [
			'image'   => $image,
			'click'   => $click,
			'popupID' => $popupID,
			'link'    => $link,
		];

		if ( ! empty( $switch ) && is_array( $switch ) ) {
			$case = array_replace( $case, $switch );
		}

		$this->add_render_attribute( 'element', 'class', 'wrapper' );
		if ( ! empty( $case['link']['url'] ?? false ) ) {
			$this->add_render_attribute( 'element', 'href', $case['link']['url'] );
		}
		if ( filter_var( $case['link']['is_external'] ?? false, FILTER_VALIDATE_BOOLEAN ) ) {
			$this->add_render_attribute( 'element', 'target', '_blank' );
		}
		if ( filter_var( $case['link']['nofollow'] ?? false, FILTER_VALIDATE_BOOLEAN ) ) {
			$this->add_render_attribute( 'element', 'rel', 'nofollow' );
		}
		if ( ! empty( $case['link']['custom_attributes'] ?? false ) ) {
			foreach ( explode( ',', $case['link']['custom_attributes'] ) as $value ) {
				$arr = explode( '|', $value );
				if ( ( $arr[0] ?? false ) && ( $arr[1] ?? false ) ) {
					$this->add_render_attribute( 'element', $arr[0], $arr[1] );
				}
			}
		}
		if ( ! empty( $case['popupID'] ) ) {
			$this->add_render_attribute( 'element', 'onclick', sprintf( 'elementorProFrontend.modules.popup.showPopup( { id: %s })', $case['popupID'] ) );
		}

		printf( '<%1$s %2$s><picture class="attachment-large size-large"><img src="%3$s" /></picture></%1$s>',
			! empty( $case['link'] ) ? 'a' : 'div',
			$this->get_render_attribute_string( 'element' ),
			$case['image']['url'] );
	}
}