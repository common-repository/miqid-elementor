<?php

namespace MIQID\Elementor\Widget;

use Elementor\{Controls_Manager, Icons_Manager, Repeater};
use MIQID\Elementor\Core\Widget_MIQID;
use MIQID\Plugin\Core\Util;

class IconList extends Widget_MIQID {

	protected function _register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => 'Content',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( Util::id( 'Deprecated' ), [
			'label'     => __( 'Deprecated, replaced by MIQID - Display Widget' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$lines = new Repeater();

		$lines->add_control( 'text', [
			'label'   => 'Text',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => true ],
		] );

		$lines->add_control( 'default_icon', [
			'label' => 'Default Icon',
			'type'  => Controls_Manager::ICONS,
		] );

		$lines->add_control( 'miqid', [
			'label'  => 'MiqId',
			'type'   => Controls_Manager::SELECT,
			'groups' => $this->getMIQIDFields(),
		] );

		$lines->add_control( 'match', [
			'label'   => 'Match',
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [ 'active' => false ],
		] );

		$lines->add_control( 'match_icon', [
			'label' => 'Match Icon',
			'type'  => Controls_Manager::ICONS,
		] );

		$this->add_control( 'lines', [
			'label'         => 'Lines',
			'type'          => Controls_Manager::REPEATER,
			'default'       => [
				[ 'text' => 'Line #1', ],
			],
			'prevent_empty' => true,
			'fields'        => $lines->get_controls(),
			'title_field'   => '{{{ text }}}',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style', [
			'label' => 'Icon',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'icon_size', [
			'label'      => 'Icon size',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 14,
			],
			'selectors'  => [
				'{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
        <ul class="elementor-icon-list-items <?= $this->get_name() ?>">
			<?php
			foreach ( $settings['lines'] as $key => $line ) {
				$miqid = explode( '.', $line["miqid"] ?? '' );
				$match = do_shortcode( sprintf( '[miqid-%s fields="%s"]', mb_strtolower( array_shift( $miqid ) ), array_shift( $miqid ) ) );
				$icon  = preg_match( '/' . $line['match'] . '/i', $match )
					? $line['match_icon']
					: $line['default_icon'];
				?>
                <li>
					<?php
					if ( ! empty( array_filter( $icon ) ) ) {
						print '<span class="elementor-icon-list-icon list-icon">';
						Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
						print '</span>';
					}
					?>
                    <span class="elementor-icon-list-text"><?= $line["text"] ?></span>
                </li>
				<?php
			}
			?>
        </ul>
		<?php
	}
}