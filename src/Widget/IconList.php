<?php

namespace MIQID\Plugin\Elementor\Widget;

use DateTime;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use MIQID\Plugin\Elementor\Control\Group\Filter;
use MIQID\Plugin\Elementor\Util;
use MyCLabs\Enum\Enum;

class IconList extends Base {
	public function get_title() {
		return __( 'Personalized iconlist', 'miqid-elementor' );
	}

	protected function _register_controls() {
		$this->start_controls_section( Util::id( 'Content' ), [
			'label' => $this->get_title(),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$lines = new Repeater();

		$lines->add_control( Util::id( 'Text' ), [
			'label' => __( 'Text' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$lines->add_control( Util::id( 'Icon', 'Default' ), [
			'label' => __( 'Default', 'miqid-elementor' ),
			'type'  => Controls_Manager::ICONS,
		] );

		$lines->add_group_control( Filter::get_type(), [
			'label' => __( 'MIQID Filter', 'miqid-elementor' ),
			'name'  => 'miqid',
		] );

		$lines->add_control( Util::id( 'Icon', 'Match' ), [
			'label' => __( 'Match', 'miqid-elementor' ),
			'type'  => Controls_Manager::ICONS,
		] );

		$this->add_control( Util::id( 'Lines' ), [
			'label'         => __( 'Lines', 'miqid-elementor' ),
			'type'          => Controls_Manager::REPEATER,
			'prevent_empty' => true,
			'fields'        => $lines->get_controls(),
			'title_field'   => sprintf( '{{{ %s }}}', Util::id( 'Text' ) ),
		] );

		$this->end_controls_section();

		$this->start_controls_section( Util::id( 'Style' ), [
			'label' => __( 'Style' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( Util::id( 'Icon', 'size' ), [
			'label'      => 'Icon size',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em' ],
			'range'      => [
				'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
			],
			'default'    => [ 'unit' => 'em', 'size' => 0.8 ],
			'selectors'  => [ '{{WRAPPER}} .elementor-icon-list-icon i' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'Icon', 'Color' ), [
			'label'     => __( 'Icon Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}  .elementor-icon-list-icon i' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( Util::id( 'Text', 'size' ), [
			'label'      => 'Text size',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em' ],
			'range'      => [
				'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
			],
			'default'    => [ 'unit' => 'em', 'size' => 1 ],
			'selectors'  => [ '{{WRAPPER}} .elementor-icon-list-text' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( Util::id( 'Text', 'Color' ), [
			'label'     => __( 'Text Color', 'miqid-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}}  .elementor-icon-list-text' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$lines    = $settings['lines'] ?? [];
		?>
        <div class="wrapper">
            <ul class="elementor-icon-list-items">
				<?php
				foreach ( $lines as $line ) {
					$text            = $line[ Util::id( 'Text' ) ];
					$text            = do_shortcode( $text );
					$filters         = (array) json_decode( $line['miqid_filter'], true );
					$matched_filters = array_filter( $filters, function ( $filter ) {
						if ( $data = $this->get_miqid_property_data( $this->get_miqid_data( $filter['class'] ), $filter['property'] ) ) {
							if ( $data instanceof Enum ) {
								return $data->equals( new $data( absint( $filter['match'] ) ) );
							} else if ( $data instanceof DateTime ) {
								return preg_match( sprintf( '/%s/i', $filter['match'] ), $data->format( 'c' ) );
							}

							return preg_match( sprintf( '/%s/i', $filter['match'] ), $data );
						}

						return false;
					} );

					$diff = array_diff( array_map( 'serialize', $filters ), array_map( 'serialize', $matched_filters ) );

					$is_match = empty( $diff );

					$icon = $is_match ? $line[ Util::id( 'Icon', 'Match' ) ] : $line[ Util::id( 'Icon', 'Default' ) ];
					?>
                    <li>
                        <span class="elementor-icon-list-icon list-icon"><?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ) ?></span>
                        <span class="elementor-icon-list-text"><?= $text ?></span>
                    </li>
					<?php
				}
				?>
            </ul>
        </div>
		<?php
	}
}