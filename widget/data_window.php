<?php

namespace MIQID\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use MIQID\Elementor\Core\Widget_MIQID;

final class Data_Window extends Widget_MIQID {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script(
			$this->get_name(),
			plugin_dir_url( __DIR__ ) . 'assets/js/data_window.js',
			[ 'elementor-frontend' ],
			date( 'Ymd-His', filemtime( plugin_dir_path( __DIR__ ) . 'assets/js/data_window.js' ) ),
			true
		);

		wp_register_style(
			$this->get_name(),
			plugin_dir_url( __DIR__ ) . 'assets/css/data_window.css',
			null,
			date( 'Ymd-His', filemtime( plugin_dir_path( __DIR__ ) . 'assets/css/data_window.css' ) )
		);
	}

	protected function _register_controls() {

		$this->start_controls_section( 'content', [
			'tab'   => Controls_Manager::TAB_CONTENT,
			'label' => __( 'Content', 'miqid-elementor' ),
		] );

		$this->add_control( 'headline', [
			'type'  => Controls_Manager::TEXT,
			'label' => __( 'Headline', 'miqid-elementor' ),
		] );

		$this->add_control( 'text', [
			'type'  => Controls_Manager::WYSIWYG,
			'label' => __( 'Text', 'miqid-elementor' ),
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'layout', [
			'tab'   => Controls_Manager::TAB_LAYOUT,
			'label' => 'Layout',
		] );

		$this->add_control( 'position', [
			'type'    => Controls_Manager::SELECT2,
			'label'   => 'Position',
			'default' => [ 'right' ],
			'options' => [
				'right' => __( 'Right', 'miqid-elementor' ),
				'left'  => __( 'Left', 'miqid-elementor' ),
				/*'top'    => __( 'Top', 'miqid-elementor' ),
				'bottom' => __( 'Bottom', 'miqid-elementor' ),*/
				//'inline' => __( 'Inline', 'miqid-elementor' ),
			],
		] );

		$this->add_control( 'button_position', [
			'type'        => Controls_Manager::SELECT2,
			'label'       => 'Button Position',
			'description' => 'Fallback to Position',
			'options'     => [
				'right' => __( 'Right', 'miqid-elementor' ),
				'left'  => __( 'Left', 'miqid-elementor' ),
				/*'top'    => __( 'Top', 'miqid-elementor' ),
				'bottom' => __( 'Bottom', 'miqid-elementor' ),*/
				//'inline' => __( 'Inline', 'miqid-elementor' ),
			],
		] );

		$this->add_control( 'state', [
			'type'  => Controls_Manager::SWITCHER,
			'label' => __( 'Start Open', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_open', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Button Open', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_open_text', [
			'type'  => Controls_Manager::TEXT,
			'label' => __( 'Text', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_open_icon', [
			'type'  => Controls_Manager::ICONS,
			'label' => __( 'Icon', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_close', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Button Close', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_close_text', [
			'type'  => Controls_Manager::TEXT,
			'label' => __( 'Text', 'miqid-elementor' ),
		] );

		$this->add_control( 'button_close_icon', [
			'type'  => Controls_Manager::ICONS,
			'label' => __( 'Icon', 'miqid-elementor' ),
		] );

		$this->end_controls_section();

	}

	protected function render() {

		$settings        = $this->get_settings_for_display();
		$position        = $settings['position'];
		$button_position = $settings['button_position'];

		if ( is_array( $position ) ) {
			$position = implode( ' ', $position );
		}

		if ( is_array( $button_position ) ) {
			$button_position = implode( ' ', $button_position );
		}

		$base = [
			'data-position' => $position,
			'data-state'    => ( filter_var( $settings['state'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ?? false ) ? 'visible' : 'hidden',
		];

		$this->add_render_attribute( 'wrapper', array_replace_recursive( $base, [
			'class' => [ 'wrapper' ],
		] ) );

		$this->add_render_attribute( 'main', array_replace_recursive( $base, [
			'class' => [ 'main', ],
		] ) );

		$this->add_render_attribute( 'headline', array_replace_recursive( $base, [
			'class' => [ 'headline' ],
		] ) );

		$this->add_render_attribute( 'content', array_replace_recursive( $base, [
			'class' => [ 'content' ],
		] ) );

		$this->add_render_attribute( 'button_wrapper', array_replace_recursive( $base, [
			'class'         => [ 'button-wrapper' ],
			'data-position' => ! empty( $button_position ) ? $button_position : $position,
		] ) );

		$this->add_render_attribute( 'button_close', array_replace_recursive( $base, [
			'data-target' => $this->get_id(),
			'class'       => [ 'button-close' ],
		] ) );

		$this->add_render_attribute( 'button_open', array_replace_recursive( $base, [
			'data-target' => $this->get_id(),
			'class'       => [ 'button-open' ],
		] ) );

		?>
        <div <?= $this->get_render_attribute_string( 'wrapper' ) ?>>
            <div <?= $this->get_render_attribute_string( 'main' ) ?>>
                <button <?= $this->get_render_attribute_string( 'button_close' ) ?>>
					<?php Icons_Manager::render_icon( $settings['button_close_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    <span><?= $settings['button_close_text'] ?></span>
                </button>
                <div <?= $this->get_render_attribute_string( 'headline' ) ?>><?= $settings['headline'] ?></div>
                <div <?= $this->get_render_attribute_string( 'content' ) ?>><?= $settings['text'] ?></div>
            </div>
            <div <?= $this->get_render_attribute_string( 'button_wrapper' ) ?>>
                <button <?= $this->get_render_attribute_string( 'button_open' ) ?>>
					<?php Icons_Manager::render_icon( $settings['button_open_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    <span><?= $settings['button_open_text'] ?></span>
                </button>
            </div>
        </div>
		<?php
	}

	public function get_script_depends() {
		return [ $this->get_name() ];
	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}
}