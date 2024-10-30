<?php

/**
 * Plugin Name:       MIQID-Elementor
 * Description:       MIQID-Elementor extend Elementor with MIQID data.
 * Version:           1.9.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            MIQ ApS
 * Author URI:        https://miqid.com/
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       miqid-elementor
 * Elementor tested up to: 3.3.1
 * Elementor Pro tested up to: 3.3.5
 */

namespace MIQID\Elementor;

require_once __DIR__ . '/vendor/autoload.php';

use Elementor\Plugin;
use MIQID\Elementor\Widget\{Display_Text, Dynamic_Images, Dynamic_Text, IconList, Input, Text_Hide_If};
use MIQID\Plugin\Elementor\Widget\{Conditional_Image, Demo, Display_Widget, Dynamic_Images as Dynamic_Images_V2, Dynamic_Text as Dynamic_Text_V2, IconList as IconList_V2, Login, Text_Visibility};
use MIQID\Plugin\Elementor\Ajax\Ajax;
use MIQID\Plugin\Elementor\Control\Group\Filter;

if ( ! defined( 'WPINC' ) ) {
	die();
}

class Elementor {
	const VERSION                   = "1.8.0";
	const MINIMUM_ELEMENTOR_VERSION = "3.0.0";
	const MINIMUM_PHP_VERSION       = "7.2";

	private static $_instance;

	public static function Instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {
		spl_autoload_register( function ( $class ) {
			if ( stripos( $class, __NAMESPACE__ ) === 0 ) {
				$include_file = implode( DIRECTORY_SEPARATOR, [
					__DIR__,
					mb_strtolower( sprintf( '%s.php', strtr( $class, [ sprintf( '%s\\', __NAMESPACE__ ) => '', '\\' => DIRECTORY_SEPARATOR, ] ) ) ),
				] );

				/** @noinspection PhpIncludeInspection */
				@include $include_file;
			}
		} );

		add_action( 'plugins_loaded', [ $this, '_language' ] );

		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}


	function _language() {
		load_plugin_textdomain( 'miqid-elementor', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function init() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			$this->_deactivate_plugin( [ $this, '_notice_php_version' ] );
		} else if ( ! is_plugin_active( 'miqid-core/miqid-core.php' ) ) {
			$this->_deactivate_plugin( [ $this, '_notice_missing_core' ] );
		} else if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
			$this->_deactivate_plugin( [ $this, '_notice_missing_elementor' ] );
		} else if ( ! did_action( 'elementor/loaded' ) ) {
			$this->_deactivate_plugin( [ $this, '_notice_missing_elementor' ] );
		} else if ( version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '<' ) ) {
			$this->_deactivate_plugin( [ $this, '_notice_elementor_version' ] );
		} else {
			add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

			add_action( 'elementor/elements/categories_registered', [ $this, 'miqid_elementor_categories' ] );

			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

			add_action( 'elementor/editor/before_enqueue_scripts', function () {
				wp_enqueue_style(
					'miqid-elementor',
					sprintf( '%s/assets/css/miqid-elementor.css', plugin_dir_url( __FILE__ ) ),
					[],
					date( 'Ymd-His', filemtime( sprintf( '%s/assets/css/miqid-elementor.css', plugin_dir_path( __FILE__ ) ) ) ) );
			} );

			Ajax::Instance();
		}
	}

	function miqid_elementor_categories( $elements_manager ) {
		$elements_manager->add_category( 'miqid', [
			'title' => "MIQID",
			'icon'  => 'fa fa-plug',
		] );
	}

	function register_controls() {
		Plugin::instance()->controls_manager->add_group_control( Filter::get_type(), new Filter() );
	}

	public function register_widgets() {
		//ToDo: Nanna skriver, mangler polering
		//Plugin::instance()->widgets_manager->register_widget_type( new Data_Window() );
		Plugin::instance()->widgets_manager->register_widget_type( new Display_Text() );
		Plugin::instance()->widgets_manager->register_widget_type( new Dynamic_Images() );
		Plugin::instance()->widgets_manager->register_widget_type( new Dynamic_Text() );
		Plugin::instance()->widgets_manager->register_widget_type( new IconList() );
		Plugin::instance()->widgets_manager->register_widget_type( new Input() );
		Plugin::instance()->widgets_manager->register_widget_type( new Text_Hide_If() );

		/* Restructured Widget */
		Plugin::instance()->widgets_manager->register_widget_type( new Conditional_Image() );
		Plugin::instance()->widgets_manager->register_widget_type( new Display_Widget() );
		Plugin::instance()->widgets_manager->register_widget_type( new Dynamic_Images_V2() );
		Plugin::instance()->widgets_manager->register_widget_type( new Dynamic_Text_V2() );
		Plugin::instance()->widgets_manager->register_widget_type( new IconList_V2() );
		Plugin::instance()->widgets_manager->register_widget_type( new Login() );
		Plugin::instance()->widgets_manager->register_widget_type( new Text_Visibility() );
	}

	/**
	 * @param callable $cb
	 */
	public function _deactivate_plugin( callable $cb ) {
		deactivate_plugins( 'miqid-elementor/miqid-elementor.php' );
		add_action( 'admin_notices', $cb );
	}

	public function _notice_missing_elementor() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'MIQID-Elementor has been deactivated, missing MIQID-elementor', 'miqid-woo' ) ?></p>
        </div>
		<?php
	}

	public function _notice_missing_core() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'MIQID-Elementor has been deactivated, missing MIQID-Core', 'miqid-woo' ) ?></p>
        </div>
		<?php
	}

	public function _notice_php_version() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?= sprintf( __( 'MIQID-Elementor has been deactivated, wrong PHP Version: %s', 'miqid-woo' ), PHP_VERSION ) ?></p>
        </div>
		<?php
	}

	public function _notice_elementor_version() {
		?>
        <div class="notice notice-error is-dismissible">
            <p><?= sprintf( __( 'MIQID-Elementor has been deactivated, wrong PHP Version: %s', 'miqid-woo' ), ELEMENTOR_VERSION ) ?></p>
        </div>
		<?php
	}
}

Elementor::Instance();