<?php

namespace MIQID\Elementor\Widget;

use Elementor\{Controls_Manager, Core\Kits\Documents\Tabs\Global_Typography, Group_Control_Border, Group_Control_Box_Shadow, Group_Control_Text_Shadow, Group_Control_Typography};
use MIQID\Elementor\Core\Widget_MIQID;

class FormSave extends Widget_MIQID {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style(
			$this->get_name(),
			plugin_dir_url( __DIR__ ) . 'assets/css/formsave.css',
			null,
			date( 'Ymd-His', filemtime( plugin_dir_path( __DIR__ ) . '/assets/css/formsave.css' ) )
		);

	}

	public function get_style_depends() {
		return [ $this->get_name() ];
	}

	protected function _register_controls() {
		$this->start_controls_section( 'content', [
			'label' => __( 'Form Save', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'button_type', [
			'label'        => __( 'Type', 'elementor' ),
			'type'         => Controls_Manager::SELECT,
			'default'      => '',
			'options'      => [
				''        => __( 'Default', 'elementor' ),
				'info'    => __( 'Info', 'elementor' ),
				'success' => __( 'Success', 'elementor' ),
				'warning' => __( 'Warning', 'elementor' ),
				'danger'  => __( 'Danger', 'elementor' ),
			],
			'prefix_class' => 'elementor-button-',
		] );

		$this->add_control( 'text', [
			'label' => __( 'Text', 'miqid-elementor' ),
			'type'  => Controls_Manager::TEXT,
		] );

		$this->add_responsive_control( 'align', [
			'label'        => __( 'Alignment', 'elementor' ),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => [
				'left'    => [
					'title' => __( 'Left', 'elementor' ),
					'icon'  => 'eicon-text-align-left',
				],
				'center'  => [
					'title' => __( 'Center', 'elementor' ),
					'icon'  => 'eicon-text-align-center',
				],
				'right'   => [
					'title' => __( 'Right', 'elementor' ),
					'icon'  => 'eicon-text-align-right',
				],
				'justify' => [
					'title' => __( 'Justified', 'elementor' ),
					'icon'  => 'eicon-text-align-justify',
				],
			],
			'prefix_class' => 'elementor%s-align-',
			'default'      => '',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'style', [
			'label' => __( 'Form Save', 'miqid-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'typography',
			'global'   => [
				'default' => Global_Typography::TYPOGRAPHY_ACCENT,
			],
			'selector' => '{{WRAPPER}} .elementor-button',
		] );

		$this->add_group_control( Group_Control_Text_Shadow::get_type(), [
			'name'     => 'text_shadow',
			'selector' => '{{WRAPPER}} .elementor-button',
		] );

		$this->add_control( 'button_text_color', [
			'label'     => __( 'Text Color', 'elementor' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		] );

		$this->add_control( 'background_color', [
			'label'     => __( 'Background Color', 'elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => 'border',
			'selector'  => '{{WRAPPER}} .elementor-button',
			'separator' => 'before',
		] );

		$this->add_control( 'border_radius', [
			'label'      => __( 'Border Radius', 'elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'button_box_shadow',
			'selector' => '{{WRAPPER}} .elementor-button',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		global $inputs, $selects;
		$settings = $this->get_settings_for_display();
		$text     = $settings['text'] ?? 'Save';

		$this->add_render_attribute( 'wrapper', 'class', 'wrapper' );
		$this->add_render_attribute( 'button', 'class', [
			'elementor-button',
			sprintf( 'elementor-button-%s', $settings['button_type'] ),
		] );

		printf( '<div %3$s><button %2$s>%1$s</button></div>',
			$text,
			$this->get_render_attribute_string( 'button' ),
			$this->get_render_attribute_string( 'wrapper' )
		);

		$data =
		$fields = array_merge(
			$inputs ?? [],
			$selects ?? []
		);

		foreach ( $data as $key => $values ) {
			$class = sprintf( 'MIQID\\Elementor\\Handler\\%s', $key );
			if ( ! class_exists( $class ) ) {
				continue;
			}

			if ( method_exists( $class::Instance(), 'Get' ) ) {
				$obj = $class::Instance()->Get();
				foreach ( $values as $_k => $_v ) {
					if ( method_exists( $obj, $this->get_method_name( $_k ) ) ) {
						$_v = $obj->{$this->get_method_name( $_k )}( $_k == 'dateOfBirth' ? 'Y-m-d' : null );
					} else if ( property_exists( $class::Instance(), $_k ) ) {
						$_v = $obj->{$_k};
					} else {
						error_log( wp_json_encode( [ 'class' => $class, 'key' => $_k ] ) );
					}
					$values[ $_k ] = $_v;
				}

				$data[ $key ] = $values;
			}
		}
		?>
        <script>
            (($) => {
                let fields = <?=json_encode( $fields )?>;

                const FormSave = (element) => {
                    let _element = $(element)

                    _element.on('click', '.elementor-button', (e) => {
                        $.each(fields, (key, fields) => {
                            $.each(fields, (field) => {
                                fields[field] = $(`[name="[${key}][${field}]"`).val();
                            })
                        });

                        let data = {}
                        Object.assign(data, fields);
                        data.action = '<?=\MIQID\Elementor\Handler\FormSave::Instance()->get_name()?>'

                        $.ajax({
                            type: 'POST',
                            url: '<?=admin_url( 'admin-ajax.php' )?>',
                            data: data
                        }).done(resp => {
                            console.log(resp)
                        })

                    })
                }

                $(window).on('elementor/frontend/init',
                    () => elementorFrontend.hooks.addAction('frontend/element_ready/miqid_elementor_widget_formsave.default', FormSave)
                )

            })(jQuery);
        </script>
		<?php

	}
}