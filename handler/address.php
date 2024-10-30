<?php

namespace MIQID\Elementor\Handler;

use MIQID\Plugin\Core\Classes\{API\Address as apiAddress, DTO\Address as dtoAddress, DTO\HttpResponse};

class Address extends dtoAddress {
	private static $_instance = null;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * @return dtoAddress|HttpResponse
	 */
	function Get() {
		return apiAddress::Instance()->GetAddress();
	}

	/**
	 * @param dtoAddress $address
	 *
	 * @return dtoAddress|HttpResponse
	 */
	function Set( dtoAddress $address ) {
		return apiAddress::Instance()->UpdateAddress( $address );
	}

}