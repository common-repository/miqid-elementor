<?php

namespace MIQID\Plugin\Elementor\Control\Group;

use Elementor\{Controls_Manager, Core\Settings\Page\Manager, Group_Control_Base, Plugin, Repeater};
use MIQID\Plugin\Elementor\Util;

final class Filter extends Group_Control_Base {

	protected static $fields;

	private static $_scheme_fields_keys = [ 'filters' ];

	public function __construct() {
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	function load_scripts() {
		wp_enqueue_script(
			'GroupControlFilter',
			sprintf( '%s/assets/js/GroupControlFilter.js', plugin_dir_url( __DIR__ . '/../../../..' ) ),
			[ 'elementor-frontend', 'underscore' ],
			date( 'Ymd-His', filemtime( sprintf( '%s/assets/js/GroupControlFilter.js', plugin_dir_path( __DIR__ . '/../../../..' ) ) ) ),
			true );
	}

	public static function get_scheme_fields_keys() {
		return self::$_scheme_fields_keys;
	}

	public static function get_type() {
		return 'miqid_filter';
	}

	protected function init_fields() {
		$fields = [];

		$fields['filter'] = [
			'label' => __( 'Filters' ),
			'type'  => Controls_Manager::TEXTAREA,
		]; // jQuery(`.elementor-control-miqid_filters`).on('click', `.elementor-control-popover-toggle-toggle`, (e)=> console.log(e));

		return $fields;
	}

	protected function add_group_args_to_field( $control_id, $field_args ) {
		parent::add_group_args_to_field( $control_id, $field_args );

		$field_args['groupPrefix'] = $this->get_controls_prefix();
		$field_args['groupType']   = 'filters';

		$args = $this->get_args();

		if ( in_array( $control_id, self::get_scheme_fields_keys() ) && ! empty( $args['scheme'] ) ) {
			$field_args['scheme'] = [
				'type'  => self::get_type(),
				'value' => $args['scheme'],
				'key'   => $control_id,
			];
		}

		return $field_args;
	}

	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name'  => 'filters',
				'starter_title' => __( 'Filters', 'miqid-elementor' ),
				'settings'      => [
					'render_type' => 'ui',
					'groupType'   => 'filters',
					'global'      => [
						'active' => true,
					],
				],
			],
		];
	}
}