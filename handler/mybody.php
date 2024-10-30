<?php

namespace MIQID\Elementor\Handler;

use MIQID\Plugin\Core\Classes\API\{MyBody as apiMyBody};
use MIQID\Plugin\Core\Classes\DTO\{HttpResponse, MyBody as dtoMyBody};

class MyBody extends dtoMyBody {
	private static $_instance = null;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * @return dtoMyBody|HttpResponse
	 */
	function Get() {
		return apiMyBody::Instance()->GetMyBody();
	}

	/**
	 * @param dtoMyBody $MyBody
	 *
	 * @return dtoMyBody|HttpResponse
	 */
	function Set( dtoMyBody $MyBody ) {
		return apiMyBody::Instance()->UpdateMyBody( $MyBody );
	}
}