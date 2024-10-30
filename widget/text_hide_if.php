<?php

namespace MIQID\Elementor\Widget;

use Elementor\Controls_Manager;
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Util;

class Text_Hide_If extends Widget_MIQID {

	protected function _register_controls() {

		$this->start_controls_section( 'content', [
			'label' => 'Content',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Deprecated' ), [
			'label'     => __( 'Deprecated, replaced by MIQID - Text Visibility' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'text', [
			'label' => 'Text',
			'type'  => Controls_Manager::WYSIWYG,
		] );

		$this->add_control( 'miqid', [
			'label'  => 'MiqId',
			'type'   => Controls_Manager::SELECT,
			'groups' => $this->getMIQIDFields(),
		] );

		$this->add_control( 'match', [
			'label'   => 'Match',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => false ],
		] );

		$this->add_control( 'negate', [
			'label'   => 'Show If',
			'type'    => Controls_Manager::SWITCHER,
			'dynamic' => [ 'active' => false ],
		] );

		$this->end_controls_section();

	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$text        = '';
		$miqid       = $settings['miqid'];
		$match       = $settings['match'];
		$negate      = filter_var( $settings['negate'], FILTER_VALIDATE_BOOLEAN );
		$miqid       = explode( '.', $miqid );
		$shortcode   = sprintf( '[miqid-%s fields="%s"]', mb_strtolower( $miqid[0] ), $miqid[1] );
		$miqid_match = do_shortcode( $shortcode );
		$pattern     = sprintf( '/%s/i', $match );

		if ( $negate && preg_match( $pattern, $miqid_match ) ) {
			$text = $settings['text'];
		} else if ( ! $negate && ! preg_match( $pattern, $miqid_match ) ) {
			$text = $settings['text'];
		}

		?>
        <div class="elementor-widget-container <?= $this->get_name() ?>">
            <div class="elementor-text-editor elementor-clearfix text">
				<?= $text ?>
            </div>
        </div>
		<?php
	}

}