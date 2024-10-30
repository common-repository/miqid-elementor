<?php

namespace MIQID\Plugin\Elementor;

use MIQID\Plugin\Core\Classes\DTO\Business\{DriversLicense, HealthInsuranceCard, MyBody, Passport, Profile, UserAddress};
use MyCLabs\Enum\Enum;
use ReflectionClass;
use ReflectionProperty;

class Util extends \MIQID\Plugin\Core\Util {
	static function id( ...$id ): string {
		if ( is_array( current( $id ) ) ) {
			$id = current( $id );
		}

		return mb_strtolower( implode( '_', $id ) );
	}

	/**
	 * @return ReflectionClass[]
	 * @throws \ReflectionException
	 */
	static function get_classes() {
		return array_map( function ( $class ) {
			return new ReflectionClass( $class );
		}, array_filter( [
			Profile::class,
			UserAddress::class,
			MyBody::class,
			Passport::class,
			DriversLicense::class,
			HealthInsuranceCard::class,
		] ) );
	}

	/**
	 * @param $class
	 *
	 * @return ReflectionProperty[]
	 */
	static function get_properties( $class ) {
		$properties      = [];
		$reflectionClass = new \ReflectionClass( $class );
		do {
			foreach ( $reflectionClass->getProperties() as $property ) {
				$properties[] = $property;
			}
		} while ( $reflectionClass = $reflectionClass->getParentClass() );

		return $properties;
	}

	static function get_enum_class( $class, $func ) {
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

	static function get_enum_values( $enum_class ) {
		$obj = [];
		if ( method_exists( $enum_class->getName(), 'values' ) ) {
			/** @var Enum $value */
			foreach ( $enum_class->getName()::values() as $value ) {
				$obj[ $value->getValue() ] = _x( $value->getKey(), $enum_class->getShortName(), 'miqid-core' );
			}
		}

		return $obj;
	}

	static function get_classes_options() {
		$Options = [];
		foreach ( self::get_classes() as $class ) {
			$Options[ $class->getName() ] = __( $class->getShortName(), 'miqid-core' );
		}

		return $Options;
	}

	static function get_properties_options( $class ) {
		$Options = [ '' => __( 'Choose' ) ];
		if ( is_string( $class ) && class_exists( $class ) ) {
			foreach ( self::get_properties( $class ) as $reflection_property ) {
				$Options[ $reflection_property->getName() ] = __( $reflection_property->getName(), 'miqid-core' );
			}
			switch ( $class ) {
				case Passport::class:
					$Options['passportfaceimage'] = __( 'Passport Face image', 'miqid-elementor' );
					$Options['passportimage']     = __( 'Passport image', 'miqid-elementor' );
					break;
				case DriversLicense::class:
					$Options['driverslicensefaceimage'] = __( 'DriversLicense Face Image', 'miqid-elementor' );
					$Options['driverslicenseimage']     = __( 'DriversLicense Image', 'miqid-elementor' );
					break;
				case HealthInsuranceCard::class:
					$Options['healthinsurancecardimage'] = __( 'HealthInsuranceCard Image', 'miqid-elementor' );
					break;
			}
		}

		return $Options;
	}

}