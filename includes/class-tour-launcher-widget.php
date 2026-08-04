<?php
/**
 * Elementor tour launcher widget.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a button users can click to start or replay a tour.
 */
final class NELXSTD_PRT_Tour_Launcher_Widget extends \Elementor\Widget_Base {
	/**
	 * Gets widget machine name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nelxstd_prt_tour_launcher';
	}

	/**
	 * Gets widget display title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Product Tour Launcher', 'nelx-product-tour' );
	}

	/**
	 * Gets widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-help-o';
	}

	/**
	 * Gets widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'nelx-product-tour' );
	}

	/**
	 * Gets widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'tour', 'guide', 'walkthrough', 'onboarding', 'nelx' );
	}

	/**
	 * Registers widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'nelxstd_prt_launcher_content',
			array(
				'label' => __( 'Launcher', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'nelxstd_prt_tour_id',
			array(
				'label'       => __( 'Tour ID', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'default',
				'placeholder' => 'default',
				'description' => __( 'Use the Tour ID assigned to your tour steps.', 'nelx-product-tour' ),
			)
		);

		$this->add_control(
			'nelxstd_prt_button_text',
			array(
				'label'   => __( 'Button Text', 'nelx-product-tour' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Take tour', 'nelx-product-tour' ),
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_align',
			array(
				'label'     => __( 'Alignment', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'nelx-product-tour' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'nelx-product-tour' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'nelx-product-tour' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'nelxstd_prt_launcher_style',
			array(
				'label' => __( 'Button', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nelxstd_prt_button_color',
			array(
				'label'     => __( 'Text Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_button_background',
			array(
				'label'     => __( 'Background Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_button_padding',
			array(
				'label'      => __( 'Padding', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 18,
					'bottom'   => 14,
					'left'     => 18,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_button_radius',
			array(
				'label'      => __( 'Border Radius', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 999,
					'right'    => 999,
					'bottom'   => 999,
					'left'     => 999,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'nelxstd_prt_button_typography',
				'label'    => __( 'Typography', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-launcher-widget',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'nelxstd_prt_button_border',
				'label'    => __( 'Border', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-launcher-widget',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nelxstd_prt_button_shadow',
				'label'    => __( 'Box Shadow', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-launcher-widget',
			)
		);

		$this->add_control(
			'nelxstd_prt_button_hover_heading',
			array(
				'label'     => __( 'Hover', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'nelxstd_prt_button_hover_color',
			array(
				'label'     => __( 'Hover Text Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_button_hover_background',
			array(
				'label'     => __( 'Hover Background', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'nelxstd_prt_button_hover_border',
				'label'    => __( 'Hover Border', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-launcher-widget:hover',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nelxstd_prt_button_hover_shadow',
				'label'    => __( 'Hover Box Shadow', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-launcher-widget:hover',
			)
		);

		$this->add_control(
			'nelxstd_prt_button_hover_lift',
			array(
				'label'      => __( 'Hover Lift', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -10,
						'max'  => 10,
						'step' => 1,
					),
				),
				'default'   => array( 'size' => -1, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-launcher-widget:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Renders widget output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tour_id  = ! empty( $settings['nelxstd_prt_tour_id'] ) ? sanitize_key( $settings['nelxstd_prt_tour_id'] ) : 'default';
		$text     = ! empty( $settings['nelxstd_prt_button_text'] ) ? $settings['nelxstd_prt_button_text'] : __( 'Take tour', 'nelx-product-tour' );

		?>
		<div class="nelxstd-prt-launcher-widget-wrap">
			<button type="button" class="nelxstd-prt-launcher-widget" data-nelxstd-prt-launch-tour="<?php echo esc_attr( $tour_id ); ?>">
				<?php echo esc_html( $text ); ?>
			</button>
		</div>
		<?php
	}
}
