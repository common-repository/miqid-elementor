<?php

namespace MIQID\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Util;

final class Dynamic_Images extends Widget_MIQID {

	public function get_icon() {
		return 'eicon-image';
	}

	protected function _register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => 'Content',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Deprecated' ), [
			'label'     => __( 'Deprecated, replaced by MIQID - Dynamic Images - V2' ),
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

		$this->add_control( 'default_image', [
			'label'   => 'Default Image',
			'type'    => Controls_Manager::MEDIA,
			'dynamic' => [ 'active' => true ],
		] );

		$this->add_responsive_control( 'align', [
			'label'        => __( 'Alignment', 'elementor' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => [
				'left'    => [
					'title' => __( 'Left', 'elementor' ),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => __( 'Center', 'elementor' ),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => __( 'Right', 'elementor' ),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => __( 'Justified', 'elementor' ),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'prefix_class' => 'elementor%s-align-',
			'default'      => '',
		] );

		$this->add_control( 'clickAction', [
			'label'   => 'Click action',
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'none'  => "None",
				'link'  => 'Link',
				'popup' => 'Popup',
			],
			'default' => 'none',
		] );

		$this->add_control( 'popupID', [
			'label'     => 'Post ID of popup',
			'condition' => [ 'clickAction' => 'popup' ],
			'type'      => Controls_Manager::TEXT,
			'dynamic'   => [ 'active' => true ],
		] );

		$this->add_control( 'link', [
			'label'     => 'Link',
			'condition' => [ 'clickAction' => 'link' ],
			'type'      => Controls_Manager::URL,
			'dynamic'   => [ 'active' => true ],
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'match', [
			'label'   => 'Text Match',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => true ],
		] );

		$repeater->add_control( 'image', [
			'label'   => 'Display image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		] );

		$repeater->add_control( 'clickAction', [
			'label'   => 'Click action',
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'none'  => "None",
				'link'  => 'Link',
				'popup' => 'Popup',
			],
			'default' => 'none',
		] );

		$repeater->add_control( 'popupID', [
			'label'     => 'Post ID of popup',
			'condition' => [ 'clickAction' => 'popup' ],
			'type'      => Controls_Manager::TEXT,
			'dynamic'   => [ 'active' => true ],
		] );

		$repeater->add_control( 'link', [
			'label'     => 'Link',
			'condition' => [ 'clickAction' => 'link' ],
			'type'      => Controls_Manager::URL,
			'dynamic'   => [ 'active' => true ],
		] );

		$this->add_control( 'switch', [
			'label'         => 'Switch cases',
			'type'          => Controls_Manager::REPEATER,
			'default'       => [
				'title' => "Item #1",
			],
			'prevent_empty' => true,
			'fields'        => $repeater->get_controls(),
			'title_field'   => '{{{ match }}}',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings             = $this->get_settings_for_display();
		$miqid_category       = (string) $settings['miqid-category'] ?? '';
		$miqid_category_field = $this->get_miqid_category_field( $miqid_category, $settings );
		$default_image        = $settings['default_image'];
		$clickAction          = $settings['clickAction'];
		$popupID              = $settings['popupID'];
		$link                 = $settings['link'];
		$switch               = $settings['switch'];

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

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf( '<ins>%s</ins>', sprintf( __( 'Bound to: %s' ), $miqid_category_field ) );
		}

		$case = [
			'image'       => $default_image,
			'clickAction' => $clickAction,
			'popupID'     => $popupID,
			'link'        => $link,
		];

		$switch = array_filter( $switch, function ( $case ) use ( $shortcode_value ) {
			return preg_match( sprintf( '/%s/i', $case['match'] ), $shortcode_value );
		} );

		if ( is_array( $switch ) && ! empty( $switch ) ) {
			$switch = array_filter( current( $switch ) );
		}

		$case = array_replace( $case, $switch );

		$element = 'div';
		if ( ! empty( $case['link'] ) ) {
			$element = 'a';
			$this->add_render_attribute( 'element', 'href', $case['link']['url'] );
			if ( filter_var( $case['link']['is_external'] ?? false, FILTER_VALIDATE_BOOLEAN ) ) {
				$this->add_render_attribute( 'element', 'target', '_blank' );
			}
			if ( filter_var( $case['link']['nofollow'] ?? false, FILTER_VALIDATE_BOOLEAN ) ) {
				$this->add_render_attribute( 'element', 'rel', 'nofollow' );
			}
			if ( ! empty( $case['link']['custom_attributes'] ) ) {
				foreach ( explode( ',', $case['link']['custom_attributes'] ) as $value ) {
					$arr = explode( '|', $value );
					if ( ( $arr[0] ?? false ) && ( $arr[1] ?? false ) ) {
						$this->add_render_attribute( 'element', $arr[0], $arr[1] );
					}
				}
			}
		}
		if ( ! empty( $case['popupID'] ) ) {
			$this->add_render_attribute( 'element', 'onclick', sprintf( 'elementorProFrontend.modules.popup.showPopup( { id: %s })', $case['popupID'] ) );
		}

		printf( '<%1$s %2$s><picture class="attachment-large size-large"><img src="%3$s"/></picture></%1$s>',
			$element,
			$this->get_render_attribute_string( 'element' ),
			$case['image']['url']
		);
	}
}