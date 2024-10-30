<?php

namespace MIQID\Plugin\Elementor\Widget;

use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Classes\DTO\{Business\DriversLicense, Business\HealthInsuranceCard, Business\MyBody, Business\Passport, Business\Profile, Business\UserAddress, Enum\FileContentResultType, FileContentResult, HttpResponse};
use MIQID\Plugin\Core\Classes\API\{Business\Certificate as api_Certificate, Business\MyBody as api_MyBody, Business\Profile as api_Profile, Business\UserAddress as api_UserAddress};
use MIQID\Plugin\Elementor\Util;

abstract class Base extends Widget_MIQID {
	function get_title() {
		return sprintf( 'MIQID - %s', strtr( $this->_title ?? get_class( $this ), [
			'MIQID\\Plugin\\Elementor\\Widget\\' => '',
			'_'                                  => ' ',
		] ) );
	}

	public function get_icon() {
		return mb_strtolower(
			sprintf( 'miqid-icon %s',
				strtr( $this->_title ?? get_class( $this ), [
					'MIQID\\Plugin\\Elementor\\Widget\\' => '',
				] )
			)
		);
	}

	/**
	 * @param string $class
	 *
	 * @return Profile|UserAddress|MyBody|Passport|DriversLicense|HealthInsuranceCard|FileContentResult|HttpResponse|null
	 */
	function get_miqid_data( string $class ) {
		switch ( $class ) {
			case Profile::class:
				return api_Profile::Instance()->GetProfile( Util::get_profileId() );
			case UserAddress::class:
				return api_UserAddress::Instance()->GetUserAddress( Util::get_profileId() );
			case MyBody::class:
				return api_MyBody::Instance()->GetMyBody( Util::get_profileId() );
			case Passport::class:
				return api_Certificate::Instance()->GetPassportCertificateInformation( Util::get_profileId() );
			case DriversLicense::class:
				return api_Certificate::Instance()->GetDriversLicenseCertificateInformation( Util::get_profileId() );
			case HealthInsuranceCard::class:
				return api_Certificate::Instance()->GetHealthInsuranceCardCertificateInformation( Util::get_profileId() );
			case FileContentResult::class:
				/** @var FileContentResult $class */
				switch ( $class->get_file_content_result_type() ) {
					case FileContentResultType::PassportImage:
						return api_Certificate::Instance()->GetProfilePassportImage( Util::get_profileId() );
					case FileContentResultType::PassportFaceImage:
						return api_Certificate::Instance()->GetProfilePassportFaceImage( Util::get_profileId() );
					case FileContentResultType::DriversLicenseImage:
						return api_Certificate::Instance()->GetProfileDriversLicenseImage( Util::get_profileId() );
					case FileContentResultType::DriversLicenseFaceImage:
						return api_Certificate::Instance()->GetDriversLicenseFaceImage( Util::get_profileId() );
					case FileContentResultType::HealthInsuranceCardImage:
						return api_Certificate::Instance()->GetHealthInsuranceCardImage( Util::get_profileId() );
				}
		}

		return null;
	}

	function get_miqid_property_data( $data, $property ) {
		if ( ! $data instanceof HttpResponse ) {
			if ( ( $method = sprintf( 'get_%s', Util::snake_case( $property ) ) ) && method_exists( $data, $method ) ) {
				return $data->$method();
			} else if ( ( $method = sprintf( 'is_%s', Util::snake_case( $property ) ) ) && method_exists( $data, $method ) ) {
				return $data->$method();
			}
		}

		return null;
	}
}