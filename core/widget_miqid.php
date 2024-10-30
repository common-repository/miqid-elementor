<?php

namespace MIQID\Elementor\Core;

use Elementor\Widget_Base;
use MIQID\Plugin\Core\Classes\DTO\{Business\DriversLicense as business_DriversLicense, Business\HealthInsuranceCard as business_HealthInsuranceCard, Business\MyBody as business_MyBody, Business\Passport as business_Passport, Business\Profile as business_Profile, Business\UserAddress as business_UserAddress, DriversLicense, FileContentResult};
use MyCLabs\Enum\Enum;
use MIQID\Elementor\Handler\{Passport, Address, MyBody, Profile};
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class Widget_MIQID extends Widget_Base {

	public $_title;

	public function get_name() {
		return strtolower( strtr( get_class( $this ), [ '\\' => '_' ] ) );
	}

	public function get_title() {
		return sprintf( __( 'MIQID - %s' ),
			$this->_title ?? strtr( get_class( $this ), [ 'MIQID\\Elementor\\Widget\\' => '', '_' => ' ' ] )
		);
	}

	public function get_categories() {
		return [ 'miqid' ];
	}

	public function get_icon() {
		return 'eicon-text';
	}

	function getMIQIDFields() {
		$options = [];
		$Classes = [
			Profile::class,
			Address::class,
			MyBody::class,
			Passport::class,
		];

		foreach ( $Classes as $i => $class ) {
			$ReflectionClass = new ReflectionClass( $class );
			$ShortName       = $ReflectionClass->getShortName();
			$class_options   = [];
			do {
				foreach ( $ReflectionClass->getProperties() as $property ) {
					$class_options[ sprintf( '%s.%s', $ShortName, $property->getName() ) ] = __( $property->getName(), 'miqid-core' );
				}
			} while ( $ReflectionClass = $ReflectionClass->getParentClass() );

			$class_options = array_filter( $class_options, function ( $key ) {
				return ! preg_match( '/instance/i', $key );
			}, ARRAY_FILTER_USE_KEY );

			$options[ $ShortName ] = [
				'label'   => __( $ShortName, 'miqid-core' ),
				'options' => $class_options,
			];
		}

		$options['Profile']['options']['Profile.DateOfBirth|dmy\-XXXX'] = __( 'CPR-nr' );
		$options['Profile']['options']['profilepassportfaceimage']      = __( 'Passport Face Image' );

//		print_r( $options );

		return $options;
	}

	function get_font_weights() {
		return [
			'100' => __( 'Thin, Hairline, Ultra-light, Extra-light' ),
			'200' => __( 'Light' ),
			'300' => __( 'Book' ),
			'400' => __( 'Regular, Normal, Plain, Roman, Standard' ),
			'500' => __( 'Medium' ),
			'600' => __( 'Semi-bold, Demi-bold' ),
			'700' => __( 'Bold' ),
			'800' => __( 'Heavy, Black, Extra-bold' ),
			'900' => __( 'Ultra-black, Extra-black, Ultra-bold, Heavy-black, Fat, Poster' ),
		];
	}

	function get_method_name( $key ) {
		return sprintf( 'get_%s', strtolower( preg_replace( [ '/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/' ], '$1_$2', $key ) ) );
	}

	function _register_css( ...$files ) {
		$class = new ReflectionClass( $this );
		$files = array_values( array_unique( array_merge( [ mb_strtolower( sprintf( '%s.css', $class->getShortName() ) ) ], $files ) ) );

		$path     = plugin_dir_path( __DIR__ ) . 'assets/css';
		$url      = plugin_dir_url( __DIR__ ) . 'assets/css';
		$handlers = [];
		foreach ( $files as $file ) {
			if ( file_exists( sprintf( '%s/%s', $path, $file ) ) ) {
				$filemtime = filemtime( sprintf( '%s/%s', $path, $file ) );
				$fileName  = basename( $file, '.js' );
				$handler   = implode( '_', array_unique( explode( '_', sprintf( '%s_%s', $this->get_name(), $fileName ) ) ) );

				wp_register_style( $handler, sprintf( '%s/%s', $url, $file ), null, date( 'Ymd-His', $filemtime ) );

				$handlers[] = $handler;
			}
		}

		return $handlers;
	}

	function _register_js( ...$files ) {
		$class = new ReflectionClass( $this );
		$files = array_values( array_unique( array_merge( [ mb_strtolower( sprintf( '%s.js', $class->getShortName() ) ) ], $files ) ) );

		$path     = plugin_dir_path( __DIR__ ) . 'assets/js';
		$url      = plugin_dir_url( __DIR__ ) . 'assets/js';
		$handlers = [];
		foreach ( $files as $file ) {
			if ( file_exists( sprintf( '%s/%s', $path, $file ) ) ) {
				$filemtime = filemtime( sprintf( '%s/%s', $path, $file ) );
				$fileName  = basename( $file, '.js' );
				$handler   = implode( '_', array_unique( explode( '_', sprintf( '%s_%s', $this->get_name(), $fileName ) ) ) );

				wp_register_script( $handler, sprintf( '%s/%s', $url, $file ), [ 'elementor-frontend', 'jquery' ], date( 'Ymd-His', $filemtime ), true );
				wp_localize_script( $handler, $fileName, [ 'admin-ajax' => admin_url( 'admin-ajax.php' ) ] );

				$handlers[] = $handler;
			}
		}

		return $handlers;
	}

	/**
	 * @return ReflectionClass[]
	 * @throws ReflectionException
	 */
	function get_classes() {
		return array_map( function ( $class ) {
			return new ReflectionClass( $class );
		}, array_filter( [
			business_Profile::class,
			business_UserAddress::class,
			business_MyBody::class,
			business_Passport::class,
			business_DriversLicense::class,
			business_HealthInsuranceCard::class,
			FileContentResult::class,
		] ) );
	}

	/**
	 * @param $class
	 *
	 * @return ReflectionProperty[]
	 */
	function get_properties( $class ) {
		$properties      = [];
		$reflectionClass = new \ReflectionClass( $class );
		do {
			foreach ( $reflectionClass->getProperties() as $property ) {
				$properties[] = $property;
			}
		} while ( $reflectionClass = $reflectionClass->getParentClass() );

		return $properties;
	}

	/**
	 * @param $class
	 * @param $func
	 *
	 * @return ReflectionClass|null
	 * @throws ReflectionException
	 */
	function get_enum_class( $class, $func ) {
		if ( is_object( $class ) ) {
			$class = get_class( $class );
		}
		if ( class_exists( $class )
			 && ( $class = new $class() )
			 && method_exists( $class, $func )
			 && ( $enum = $class->$func() )
			 && $enum instanceof Enum ) {
			return new ReflectionClass( get_class( $enum ) );
		}

		return null;
	}

	/**
	 * @param ReflectionClass $enum_class
	 *
	 * @return array
	 */
	function get_enum_values( $enum_class ) {
		$obj = [];
		if ( method_exists( $enum_class->getName(), 'values' ) ) {
			/** @var Enum $value */
			foreach ( $enum_class->getName()::values() as $value ) {
				$obj[ $value->getValue() ] = _x( $value->getKey(), $enum_class->getShortName(), 'miqid-core' );
			}
		}

		return $obj;
	}

	function get_enum_values_based_on_text( $enum_class ) {
		$obj = [];
		if ( method_exists( $enum_class->getName(), 'values' ) ) {
			/** @var Enum $value */
			foreach ( $enum_class->getName()::values() as $value ) {
				$obj[ $value->getValue() ] = _x( $value->getKey(), $enum_class->getShortName(), 'miqid-core' );
			}
		}

		return $obj;
	}

	function get_classes_options() {
		$Options = [];
		foreach ( $this->get_classes() as $class ) {
			$Options[ $class->getName() ] = __( $class->getShortName(), 'miqid-elementor' );
		}

		return $Options;

	}

	function get_properties_options( $class ) {
		$Options = [ '' => __( 'Choose' ), ];
		if ( is_string( $class ) ) {
			if ( class_exists( $class ) ) {
				foreach ( $this->get_properties( $class ) as $reflection_property ) {
					$Options[ $reflection_property->getName() ] = __( $reflection_property->getName(), 'miqid-core' );
				}
				switch ( $class ) {
					case business_Passport::class:
						$Options['passportfaceimage'] = __( 'Passport Face image', 'miqid-elementor' );
						$Options['passportimage']     = __( 'Passport image', 'miqid-elementor' );
						break;
					case business_DriversLicense::class:
						$Options['driverslicensefaceimage'] = __( 'DriversLicense Face Image', 'miqid-elementor' );
						$Options['driverslicenseimage']     = __( 'DriversLicense Image', 'miqid-elementor' );
						break;
					case business_HealthInsuranceCard::class:
						$Options['healthinsurancecardimage'] = __( 'HealthInsuranceCard Image', 'miqid-elementor' );
						break;
				}
			} else if ( $class === 'Custom' ) {

			}
		}

		return $Options;
	}

	function get_miqid_category_field( ?string $miqid_category, ?array $settings ): string {
		$miqid_category_field = $settings[ strtr( $miqid_category, [ '\\' => '-' ] ) ] ?? '';
		if ( empty( $miqid_category_field ) ) {
			$miqid_category_field = $settings['miqid'] ?? '';
		}
		if ( empty( $miqid_category_field ) ) {
			$miqid_category_field = $settings['field'] ?? '';
		}

		return $miqid_category_field;
	}
}