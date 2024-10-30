<?php

namespace MIQID\Elementor\Handler;

use MIQID\Plugin\Core\Classes\DTO\HttpResponse;

class FormSave {
	private static $_instance;
	private        $name;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self ();
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->name = strtr( get_class( $this ), [ '\\' => '_' ] );

		add_action( sprintf( 'wp_ajax_%s', $this->name ), [ $this, 'handler' ] );
		add_action( sprintf( 'wp_ajax_nopriv_%s', $this->name ), [ $this, 'handler' ] );
	}

	public function get_name(): string {
		return $this->name;
	}

	function handler() {
		$resp = [];

		foreach ( $_POST as $key => $values ) {
			$class = sprintf( 'MIQID\\Elementor\\Handler\\%s', $key );
			if ( ! class_exists( $class ) ) {
				continue;
			}

			if ( ! method_exists( $class, 'Instance' ) || ! method_exists( $class, 'Get' ) ) {
				continue;
			}

			if ( ( $obj = $class::Instance()->Get() ) && ! $obj instanceof HttpResponse ) {
				$vars = $obj->jsonSerialize();
				foreach ( $values as $field => $value ) {
					if ( key_exists( $field, $vars ) ) {
						$vars[ $field ] = $value;
					}
				}

				$resp[ $key ] = $class::Instance()->Set( new $class( $vars ) );
			}
		}

		wp_send_json( $resp );
	}
}