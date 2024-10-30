<?php

namespace MIQID\Elementor\Handler;

use MIQID\Plugin\Core\Classes\API\{Passport as apiPassport};
use MIQID\Plugin\Core\Classes\DTO\{HttpResponse, Passport as dtoPassport};

class Passport extends dtoPassport {
	private static $_instance = null;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * @return dtoPassport|HttpResponse
	 */
	function Get() {
		return apiPassport::Instance()->GetPassport();
	}

	/**
	 * @param dtoPassport $Passport
	 *
	 * @return dtoPassport|HttpResponse
	 */
	function Set( dtoPassport $Passport ) {
		return apiPassport::Instance()->UpdatePassport( $Passport );
	}
}