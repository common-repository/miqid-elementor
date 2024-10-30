<?php

namespace MIQID\Elementor\Handler;

use MIQID\Plugin\Core\Classes\{API\Profile as apiProfile, DTO\Profile as dtoProfile};

class Profile extends dtoProfile {
	private static $_instance = null;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	function Get( ) {
		return apiProfile::Instance()->GetProfile();
	}

	/**
	 * @param dtoProfile $profile
	 *
	 * @return dtoProfile|
	 */
	function Set( dtoProfile $profile ) {
		return apiProfile::Instance()->UpdateProfile( $profile );
	}
}