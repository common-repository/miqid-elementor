<?php

namespace MIQID\Plugin\Elementor\Ajax;

use DateTime;
use MIQID\Plugin\Core\Classes\DTO\Business\{DriversLicense, HealthInsuranceCard, MyBody, Passport, Profile, UserAddress};
use MIQID\Plugin\Elementor\Util;
use MyCLabs\Enum\Enum;

class Ajax {
	private static $instance;

	static function Instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_nopriv_miqid_classes', [ $this, 'miqid_classes' ] );
		add_action( 'wp_ajax_miqid_classes', [ $this, 'miqid_classes' ] );
		add_action( 'wp_ajax_nopriv_miqid_properties', [ $this, 'miqid_properties' ] );
		add_action( 'wp_ajax_miqid_properties', [ $this, 'miqid_properties' ] );
		add_action( 'wp_ajax_nopriv_miqid_property_match', [ $this, 'miqid_property_match' ] );
		add_action( 'wp_ajax_miqid_property_match', [ $this, 'miqid_property_match' ] );
	}

	function miqid_classes() {
		$classes = array_map( function ( $class ) {
			$class        = new \ReflectionClass( $class );
			$class->title = __( $class->getShortName(), 'miqid-core' );

			return $class;
		}, array_filter( [
			Profile::class,
			UserAddress::class,
			MyBody::class,
			Passport::class,
			DriversLicense::class,
			HealthInsuranceCard::class,
		], 'class_exists' ) );

		wp_send_json( $classes );
	}

	function miqid_properties() {
		$properties = [];
		if ( ( $class = $_REQUEST['class'] ?? '' ) &&
		     ( $class = wp_unslash( $class ) ) &&
		     class_exists( $class ) ) {
			$reflection_class = new \ReflectionClass( $class );
			do {
				foreach ( $reflection_class->getProperties() as $reflection_property ) {
					$reflection_property->title = __( $reflection_property->getName(), 'miqid-core' );
					$properties[]               = $reflection_property;
				}
			} while ( $reflection_class = $reflection_class->getParentClass() );

			switch ( $class ) {
				case Passport::class:
					$properties[] = [ 'name' => 'passportfaceimage', 'title' => __( 'Passport Face Image', 'miqid-elementor' ) ];
					$properties[] = [ 'name' => 'passportimage', 'title' => __( 'Passport Image', 'miqid-elementor' ) ];
					break;
				case DriversLicense::class:
					$properties[] = [ 'name' => 'driverslicensefaceimage', 'title' => __( 'DriversLicense Face Image', 'miqid-elementor' ) ];
					$properties[] = [ 'name' => 'driverslicenseimage', 'title' => __( 'DriversLicense Image', 'miqid-elementor' ) ];
					break;
				case HealthInsuranceCard::class:
					$properties[] = [ 'name' => 'healthinsurancecardimage', 'title' => __( 'HealthInsuranceCard Image', 'miqid-elementor' ) ];
					break;
			}
		}

		wp_send_json( $properties );
	}

	function miqid_property_match() {
		$html = [
			'Start' => '<div class="elementor-control elementor-control-miqid_match elementor-control-type-text elementor-label-inline elementor-control-separator-default elementor-control-dynamic"
     style="padding-left: 0; padding-right: 0;">
    <div class="elementor-control-content">
        <div class="elementor-control-field">
            <label for="miqid_filter_match-${i}" class="elementor-control-title">MIQID Match</label>
            <div class="elementor-control-input-wrapper elementor-control-unit-5">
				',
			'Input' => '',
			'End'   => '
            </div>
        </div>
    </div>
</div>',
		];
		if ( ( $class = $_REQUEST['class'] ?? '' ) &&
		     ( $class = wp_unslash( $class ) ) &&
		     class_exists( $class ) ) {
			$class            = new $class();
			$reflection_class = new \ReflectionClass( $class );
			if ( $property = $_REQUEST['property'] ?? '' ) {
				if ( ( $method = sprintf( 'get_%s', Util::snake_case( $property ) ) ) &&
				     method_exists( $class, $method ) ) {
					$obj = $class->$method();
					if ( $obj instanceof Enum ) {
						$select = [];
						foreach ( $obj::values() as $value ) {
							$select[] = sprintf( '<option value="%1$s">%2$s</option>',
								$value->getValue(),
								_x( $value->getKey(), $reflection_class->getShortName(), 'miqid-core' ) );
						}
						$html['Start'] = strtr( $html['Start'], [ 'elementor-control-type-text' => 'elementor-control-type-select' ] );
						$html['Input'] = sprintf( '<select id="miqid_filter_match-${i}">%s</select>', implode( $select ) );
					} else {
						$html['Input'] = '<input type="text" id="miqid_filter_match-${i}">';
					}
				} else if ( ( $method = sprintf( 'is_%s', Util::snake_case( $property ) ) ) &&
				            method_exists( $class, $method ) ) {
					$html['Input'] = '<input type="checkbox" id="miqid_filter_match-${i}">';
				}
			}

		}
		$html = strtr( implode( $html ), [
			'${i}' => $_REQUEST['i'],
		] );
		wp_send_json( [
			'html' => $html,
		] );
	}
}