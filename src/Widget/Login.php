<?php

namespace MIQID\Plugin\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use MIQID\Plugin\Core\Classes\API\Business\Profile;
use MIQID\Plugin\Core\Classes\DTO\HttpResponse;
use MIQID\Plugin\Elementor\Util;

final class Login extends Base {
	private $CSS = [];

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->CSS = $this->_register_css();
	}

	public function get_style_depends() {
		return $this->CSS;
	}

	public function get_title() {
		return __( 'MIQID login', 'miqid-elementor' );
	}

	protected function _register_controls() {
		// <editor-fold desc="Username Content">
		$this->start_controls_section( 'username_content', [
			'label' => _x( 'Username', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'username_label', [
			'label'   => _x( 'Label', 'Login Widget', 'miqid-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Username' ),
		] );

		$this->add_control( 'username_placeholder', [
			'label' => _x( 'Placeholder', 'Login Widget', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Password Content">
		$this->start_controls_section( 'password_content', [
			'label' => _x( 'Password', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'password_label', [
			'label'   => _x( 'Label', 'Login Widget', 'miqid-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Password' ),
		] );

		$this->add_control( 'password_placeholder', [
			'label' => _x( 'Placeholder', 'Login Widget', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Content Button">
		$this->start_controls_section( Util::id( 'content', 'button' ), [
			'label' => _x( 'Button', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'content', 'button', 'text' ), [
			'label'   => _x( 'Text', 'Login Widget', 'miqid-elementor' ),
			'type'    => Controls_Manager::TEXT,
			'default' => __( 'Log in' ),
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Content Redirect">
		$this->start_controls_section( Util::id( 'content', 'redirect' ), [
			'label' => _x( 'Redirect', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'content', 'redirect', 'to' ), [
			'label' => _x( 'To', 'Login Widget', 'miqid-elementor' ),
			'type'  => Controls_Manager::URL,
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Content Logged In">
		$this->start_controls_section( Util::id( 'content', 'logged_in' ), [
			'label' => _x( 'Logged In', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'content', 'redirect', 'always' ), [
			'label' => _x( 'Auto Redirect', 'Login Widget', 'miqid-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );

		$this->add_control( Util::id( 'content', 'logged_in', 'wysiwyg' ), [
			'label'     => _x( 'Text', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::WYSIWYG,
			'condition' => [
				Util::id( 'content', 'redirect', 'always' ) => '',
			],
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Styling Username">
		$this->start_controls_section( Util::id( 'style', 'username' ), [
			'label' => _x( 'Username Style', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( Util::id( 'style', 'username', 'flex-direction' ), [
			'label'     => _x( 'Direction', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'row'    => __( 'Row', 'miqid-elementor' ),
				'column' => __( 'Column', 'miqid-elementor' ),
			],
			'default'   => 'column',
			'selectors' => [ '{{WRAPPER}} .login-username' => 'flex-direction: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'align-items' ), [
			'label'     => _x( 'Align', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'flex-start' => __( 'Top / Left', 'elementor' ),
				'center'     => __( 'Middle', 'elementor' ),
				'flex-end'   => __( 'Bottom / Right', 'elementor' ),
			],
			'selectors' => [ '{{WRAPPER}} .login-username' => 'align-items: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'input', 'width' ), [
			'label'      => _x( 'Input Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 1000, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 100, 'unit' => '%' ],
			'selectors'  => [ '{{WRAPPER}} .login-username input' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'input', 'margin' ), [
			'label'      => _x( 'Input Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-username input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'input', 'padding' ), [
			'label'      => _x( 'Input Padding', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-username input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'border', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'username', 'border', 'width' ), [
			'label'      => _x( 'Border Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-username input' => 'border-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'border', 'style' ), [
			'label'     => __( 'Border Style', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'none'   => __( 'None', 'miqid-elementor' ),
				'solid'  => __( 'Solid', 'miqid-elementor' ),
				'double' => __( 'Double', 'miqid-elementor' ),
				'dotted' => __( 'Dotted', 'miqid-elementor' ),
			],
			'default'   => 'none',
			'selectors' => [ '{{WRAPPER}} .login-username input' => 'border-style: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'border', 'color' ), [
			'label'     => __( 'Border Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-username input' => 'border-color: {{VALUE}}' ],
		] );

		$this->add_control( Util::id( 'style', 'username', 'margin' ), [
			'label'      => _x( 'Container Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-username' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Styling Password">
		$this->start_controls_section( Util::id( 'style', 'password' ), [
			'label' => _x( 'Password Style', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( Util::id( 'style', 'password', 'flex-direction' ), [
			'label'     => _x( 'Direction', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'row'    => __( 'Row', 'miqid-elementor' ),
				'column' => __( 'Column', 'miqid-elementor' ),
			],
			'default'   => 'column',
			'selectors' => [ '{{WRAPPER}} .login-password' => 'flex-direction: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'align-items' ), [
			'label'     => _x( 'Align', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'flex-start' => __( 'Top / Left', 'elementor' ),
				'center'     => __( 'Middle', 'elementor' ),
				'flex-end'   => __( 'Bottom / Right', 'elementor' ),
			],
			'selectors' => [ '{{WRAPPER}} .login-password' => 'align-items: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'input', 'width' ), [
			'label'      => _x( 'Input Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 1000, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 100, 'unit' => '%' ],
			'selectors'  => [ '{{WRAPPER}} .login-password input' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'input', 'margin' ), [
			'label'      => _x( 'Input Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-password input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'input', 'padding' ), [
			'label'      => _x( 'Input Padding', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-password input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'border', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'password', 'border', 'width' ), [
			'label'      => _x( 'Border Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-password input' => 'border-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'border', 'style' ), [
			'label'     => __( 'Border Style', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'none'   => __( 'None', 'miqid-elementor' ),
				'solid'  => __( 'Solid', 'miqid-elementor' ),
				'double' => __( 'Double', 'miqid-elementor' ),
				'dotted' => __( 'Dotted', 'miqid-elementor' ),
			],
			'default'   => 'none',
			'selectors' => [ '{{WRAPPER}} .login-password input' => 'border-style: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'border', 'color' ), [
			'label'     => __( 'Border Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-password input' => 'border-color: {{VALUE}}' ],
		] );

		$this->add_control( Util::id( 'style', 'password', 'margin', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'password', 'margin' ), [
			'label'      => _x( 'Container Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
		// </editor-fold>
		// <editor-fold desc="Styling Button">
		$this->start_controls_section( Util::id( 'style', 'button' ), [
			'label' => _x( 'Button Style', 'Login Widget', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( Util::id( 'style', 'button', 'flex-direction' ), [
			'label'     => _x( 'Direction', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				//'row'    => __( 'Row', 'miqid-elementor' ),
				'column' => __( 'Column', 'miqid-elementor' ),
			],
			'default'   => 'column',
			'selectors' => [ '{{WRAPPER}} .login-button' => 'flex-direction: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'align-items' ), [
			'label'     => _x( 'Align', 'Login Widget', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'flex-start' => __( 'Left', 'elementor' ),
				'center'     => __( 'Center', 'elementor' ),
				'flex-end'   => __( 'Right', 'elementor' ),
			],
			'default'   => 'flex-end',
			'selectors' => [ '{{WRAPPER}} .login-button' => 'align-items: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'input', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'button', 'input', 'width' ), [
			'label'      => _x( 'Button Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 1000 ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 150, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-button button' => 'width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'input', 'margin' ), [
			'label'      => _x( 'Button Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-button button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'input', 'padding' ), [
			'label'      => _x( 'Button Padding', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-button button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'border', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'button', 'border', 'width' ), [
			'label'      => _x( 'Border Width', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'size' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-button button' => 'border-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'border', 'style' ), [
			'label'     => __( 'Border Style', 'miqid-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'none'   => __( 'None', 'miqid-elementor' ),
				'solid'  => __( 'Solid', 'miqid-elementor' ),
				'double' => __( 'Double', 'miqid-elementor' ),
				'dotted' => __( 'Dotted', 'miqid-elementor' ),
			],
			'default'   => 'none',
			'selectors' => [ '{{WRAPPER}} .login-button button' => 'border-style: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'border', 'color' ), [
			'label'     => __( 'Border Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button' => 'border-color: {{VALUE}}' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'color', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'button', 'background-color' ), [
			'label'     => __( 'Background Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button' => 'background-color: {{VALUE}} !important' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'color' ), [
			'label'     => __( 'Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button' => 'color: {{VALUE}} !important' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'hover', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'button', 'border', 'color', 'hover' ), [
			'label'     => __( 'Hover: Border Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button:hover' => 'border-color: {{VALUE}} !important' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'background-color', 'hover' ), [
			'label'     => __( 'Hover: Background Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button:hover' => 'background-color: {{VALUE}} !important' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'color', 'hover' ), [
			'label'     => __( 'Hover: Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .login-button button:hover' => 'color: {{VALUE}} !important' ],
		] );

		$this->add_control( Util::id( 'style', 'button', 'margin', 'divider' ), [ 'type' => Controls_Manager::DIVIDER ] );

		$this->add_control( Util::id( 'style', 'button', 'margin' ), [
			'label'      => _x( 'Container Margin', 'Login Widget', 'miqid-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ '%', 'px', 'em' ],
			'range'      => [
				'%'  => [ 'min' => 0, 'max' => 100, ],
				'px' => [ 'min' => 0, 'max' => 10, ],
				'em' => [ 'min' => 0.1, 'max' => 1, ],
			],
			'default'    => [ 'top' => 0, 'right' => 0, 'bottom' => 10, 'left' => 0, 'unit' => 'px' ],
			'selectors'  => [ '{{WRAPPER}} .login-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
		// </editor-fold>
	}

	protected function render() {
		$settings                  = $this->get_settings_for_display();
		$username_label            = $settings['username_label'];
		$username_placeholder      = $settings['username_placeholder'];
		$password_label            = $settings['password_label'];
		$password_placeholder      = $settings['password_placeholder'];
		$content_button_text       = $settings[ Util::id( 'content', 'button', 'text' ) ] ?? '';
		$content_redirect_to       = $settings[ Util::id( 'content', 'redirect', 'to' ) ] ?? '';
		$content_redirect_always   = $settings[ Util::id( 'content', 'redirect', 'always' ) ];
		$content_logged_in_wysiwyg = $settings[ Util::id( 'content', 'logged_in', 'wysiwyg' ) ] ?? '';

		if ( empty( $content_redirect_to['url'] ) ) {
			$content_redirect_to['url'] = get_permalink();
		}

		if ( ! empty( $_GET['redirect_to'] ) ) {
			$content_redirect_to['url'] = $_GET['redirect_to'];
			$content_redirect_always    = true;
		}

		$this->add_render_attribute( 'username_label', 'for', 'username' );
		$this->add_render_attribute( 'username_input', 'id', 'username' );
		$this->add_render_attribute( 'username_input', 'type', 'text' );
		$this->add_render_attribute( 'username_input', 'name', 'log' );
		$this->add_render_attribute( 'username_input', 'class', 'input' );
		$this->add_render_attribute( 'username_input', 'placeholder', $username_placeholder );

		$this->add_render_attribute( 'password_label', 'for', 'password' );
		$this->add_render_attribute( 'password_input', 'id', 'password' );
		$this->add_render_attribute( 'password_input', 'type', 'password' );
		$this->add_render_attribute( 'password_input', 'name', 'pwd' );
		$this->add_render_attribute( 'password_input', 'class', 'input' );
		$this->add_render_attribute( 'password_input', 'placeholder', $password_placeholder );

		if ( ! Plugin::$instance->editor->is_edit_mode()
		     && ( $profileId = Util::get_user_jwt()->get_jwt_payload()->get_profile_id() )
		     && ( $profile = Profile::Instance()->GetProfile( $profileId ) )
		     && ! $profile instanceof HttpResponse ) {
			if ( $content_redirect_always ) {
				if ( $content_redirect_to['url'] !== get_permalink() ) {
					printf( '<script>location.href = "%s";</script>', $content_redirect_to['url'] );
				}
			} else {
				printf( $content_logged_in_wysiwyg );
			}
		} else {
			?>
            <form method="post" action="<?= esc_url( admin_url( 'admin-post.php' ) ) ?>">
                <div class="inputs">
                    <p class="login-username">
						<?php
						if ( ! empty( $username_label ) ) {
							printf( '<label %1$s>%2$s</label>', $this->get_render_attribute_string( 'username_label' ), $username_label );
						}
						printf( '<input %1$s />', $this->get_render_attribute_string( 'username_input' ) );
						?>
                    </p>
                    <p class="login-password">
						<?php
						if ( ! empty( $password_label ) ) {
							printf( '<label %1$s>%2$s</label>', $this->get_render_attribute_string( 'password_label' ), $password_label );
						}
						printf( '<input %1$s />', $this->get_render_attribute_string( 'password_input' ) );
						?>
                    </p>
                </div>
                <p class="login-button">
					<?php
					printf( '<button type="submit" class="button button-primary">%s</button>', $content_button_text );
					printf( '<input type="hidden" name="redirect_to" value="%s" />', $content_redirect_to['url'] );
					wp_nonce_field( - 1, '_wpnonce', true );
					?>
                    <input type="hidden" name="action" value="miqid_login">
                </p>
            </form>
			<?php
			if ( Plugin::$instance->editor->is_edit_mode() ) {
				printf( '<ins>Only for Preview</ins>' );
				printf( $content_logged_in_wysiwyg );
			}
		}
	}
}