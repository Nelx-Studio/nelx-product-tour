<?php
/**
 * Elementor floating tour replay button widget.
 *
 * This widget is intentionally independent from individual tour-step styling.
 * It is rendered as a fixed Elementor widget wrapper so it does not participate
 * in header/container layout flow.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders a configurable floating button that replays a specific product tour.
 */
final class NELXSTD_PRT_Floating_Replay_Widget extends \Elementor\Widget_Base {

	/**
	 * Gets widget machine name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nelxstd_prt_floating_replay';
	}

	/**
	 * Gets widget display title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Floating Tour Replay Button', 'nelx-product-tour' );
	}

	/**
	 * Gets widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-refresh';
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
		return array( 'tour', 'replay', 'floating', 'guide', 'walkthrough', 'onboarding', 'nelx' );
	}

	/**
	 * Registers widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_button_controls();
		$this->register_hover_controls();
		$this->register_close_controls();
	}

	/**
	 * Registers content controls in the requested order.
	 *
	 * @return void
	 */
	private function register_content_controls() {
		$this->start_controls_section(
			'nelxstd_prt_floating_content',
			array(
				'label' => __( 'Floating Tour Replay Button', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_button_text',
			array(
				'label'   => __( 'Button Text', 'nelx-product-tour' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Replay tour', 'nelx-product-tour' ),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_tour_id',
			array(
				'label'       => __( 'Tour ID', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'default',
				'placeholder' => 'default',
				'description' => __( 'Use the Tour ID assigned to the tour steps you want this floating button to replay.', 'nelx-product-tour' ),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_show_preview',
			array(
				'label'        => __( 'Show Replay Floating Button Preview', 'nelx-product-tour' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'nelx-product-tour' ),
				'label_off'    => __( 'Hide', 'nelx-product-tour' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Editor-only preview. Turn this on to display the floating replay button while styling it. It does not affect the public frontend.', 'nelx-product-tour' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers normal button styling controls.
	 *
	 * @return void
	 */
	private function register_button_controls() {
		$this->start_controls_section(
			'nelxstd_prt_floating_button_style',
			array(
				'label' => __( 'Button', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_background_type',
			array(
				'label'   => __( 'Background Type', 'nelx-product-tour' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'original',
				'options' => array(
					'original' => __( 'Original Gradient (Default)', 'nelx-product-tour' ),
					'classic'  => __( 'Classic', 'nelx-product-tour' ),
					'gradient' => __( 'Gradient', 'nelx-product-tour' ),
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_background',
			array(
				'label'     => __( 'Background Color (Classic)', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'condition' => array(
					'nelxstd_prt_floating_background_type' => 'classic',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-classic-background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_gradient_color_a',
			array(
				'label'     => __( 'Gradient Color 1', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'condition' => array(
					'nelxstd_prt_floating_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-gradient-a: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_gradient_color_b',
			array(
				'label'     => __( 'Gradient Color 2', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#00b7c2',
				'condition' => array(
					'nelxstd_prt_floating_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-gradient-b: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_gradient_angle',
			array(
				'label'      => __( 'Gradient Angle', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => 135,
					'unit' => 'deg',
				),
				'condition' => array(
					'nelxstd_prt_floating_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-gradient-angle: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_color',
			array(
				'label'     => __( 'Text Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_typography',
				'label'    => __( 'Typography', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-button',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_border',
				'label'    => __( 'Border', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-button',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_shadow',
				'label'    => __( 'Box Shadow', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-button',
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_floating_padding',
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
					'{{WRAPPER}} .nelxstd-prt-floating-replay-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_floating_radius',
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
					'{{WRAPPER}} .nelxstd-prt-floating-replay-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers hover styling controls.
	 *
	 * @return void
	 */
	private function register_hover_controls() {
		$this->start_controls_section(
			'nelxstd_prt_floating_hover_style',
			array(
				'label' => __( 'Hover', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_background_type',
			array(
				'label'   => __( 'Hover Background Type', 'nelx-product-tour' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'same',
				'options' => array(
					'same'     => __( 'Same as Normal', 'nelx-product-tour' ),
					'classic'  => __( 'Classic', 'nelx-product-tour' ),
					'gradient' => __( 'Gradient', 'nelx-product-tour' ),
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_background',
			array(
				'label'     => __( 'Hover Background Color (Classic)', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'condition' => array(
					'nelxstd_prt_floating_hover_background_type' => 'classic',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-hover-classic-background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_gradient_color_a',
			array(
				'label'     => __( 'Hover Gradient Color 1', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'condition' => array(
					'nelxstd_prt_floating_hover_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-hover-gradient-a: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_gradient_color_b',
			array(
				'label'     => __( 'Hover Gradient Color 2', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#00b7c2',
				'condition' => array(
					'nelxstd_prt_floating_hover_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-hover-gradient-b: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_gradient_angle',
			array(
				'label'      => __( 'Hover Gradient Angle', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => 135,
					'unit' => 'deg',
				),
				'condition' => array(
					'nelxstd_prt_floating_hover_background_type' => 'gradient',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-widget' => '--nelxstd-prt-floating-widget-hover-gradient-angle: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_color',
			array(
				'label'     => __( 'Hover Text Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-button:hover, {{WRAPPER}} .nelxstd-prt-floating-replay-button:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_hover_border',
				'label'    => __( 'Hover Border', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-button:hover, {{WRAPPER}} .nelxstd-prt-floating-replay-button:focus-visible',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_hover_shadow',
				'label'    => __( 'Hover Box Shadow', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-button:hover, {{WRAPPER}} .nelxstd-prt-floating-replay-button:focus-visible',
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_hover_lift',
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
				'default'  => array(
					'size' => -1,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-button:hover, {{WRAPPER}} .nelxstd-prt-floating-replay-button:focus-visible' => 'transform: translateY({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Registers close-button styling and positioning controls.
	 *
	 * @return void
	 */
	private function register_close_controls() {
		$this->start_controls_section(
			'nelxstd_prt_floating_close_style',
			array(
				'label' => __( 'Close Button', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_background',
			array(
				'label'     => __( 'Background', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_color',
			array(
				'label'     => __( 'Color', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1e3a8a',
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_close_border',
				'label'    => __( 'Border', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-close',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nelxstd_prt_floating_close_shadow',
				'label'    => __( 'Box Shadow', 'nelx-product-tour' ),
				'selector' => '{{WRAPPER}} .nelxstd-prt-floating-replay-close',
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_size',
			array(
				'label'      => __( 'Close Button Size', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 14,
						'max'  => 44,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => 22,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc({{SIZE}}{{UNIT}} * 0.72);',
				),
			)
		);

		$this->add_responsive_control(
			'nelxstd_prt_floating_close_padding',
			array(
				'label'      => __( 'Padding', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 4,
					'right'    => 4,
					'bottom'   => 4,
					'left'     => 4,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_x_position',
			array(
				'label'      => __( 'X Position (Right)', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -50,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => -11,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_y_position',
			array(
				'label'      => __( 'Y Position (Top)', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -50,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => -11,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nelxstd_prt_floating_close_hover_lift',
			array(
				'label'      => __( 'Close Hover Lift', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => -6,
						'max'  => 6,
						'step' => 1,
					),
				),
				'default'  => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .nelxstd-prt-floating-replay-close:hover, {{WRAPPER}} .nelxstd-prt-floating-replay-close:focus-visible' => 'transform: translateY({{SIZE}}{{UNIT}}) scale(1.06);',
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
		$settings  = $this->get_settings_for_display();
		$is_editor = class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->editor )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( $is_editor && 'yes' !== ( $settings['nelxstd_prt_floating_show_preview'] ?? '' ) ) {
			return;
		}

		$tour_id = ! empty( $settings['nelxstd_prt_floating_tour_id'] )
			? sanitize_key( $settings['nelxstd_prt_floating_tour_id'] )
			: 'default';

		$text = ! empty( $settings['nelxstd_prt_floating_button_text'] )
			? $settings['nelxstd_prt_floating_button_text']
			: __( 'Replay tour', 'nelx-product-tour' );

		$background_type = ! empty( $settings['nelxstd_prt_floating_background_type'] )
			? sanitize_key( $settings['nelxstd_prt_floating_background_type'] )
			: 'original';

		$hover_type = ! empty( $settings['nelxstd_prt_floating_hover_background_type'] )
			? sanitize_key( $settings['nelxstd_prt_floating_hover_background_type'] )
			: 'same';

		/*
		 * Fix the widget at the viewport level using the Elementor widget wrapper
		 * itself. This prevents the widget from participating in a header/container
		 * flex or grid layout. No DOM reparenting or frontend layout mutation is used.
		 */
		$this->add_render_attribute(
			'_wrapper',
			array(
				'style' => 'position:fixed!important;top:auto!important;right:22px!important;bottom:22px!important;left:auto!important;z-index:999980!important;width:max-content!important;max-width:calc(100vw - 44px)!important;height:max-content!important;display:inline-flex!important;overflow:visible!important;box-sizing:border-box!important;margin:0!important;padding:0!important;',
			)
		);

		?>
		<div
			class="nelxstd-prt-floating-replay-widget"
			data-nelxstd-prt-floating-tour="<?php echo esc_attr( $tour_id ); ?>"
			data-nelxstd-prt-floating-background-type="<?php echo esc_attr( $background_type ); ?>"
			data-nelxstd-prt-floating-hover-background-type="<?php echo esc_attr( $hover_type ); ?>"
		>
			<button type="button" class="nelxstd-prt-floating-replay-button">
				<?php echo esc_html( $text ); ?>
			</button>

			<button
				type="button"
				class="nelxstd-prt-floating-replay-close"
				aria-label="<?php echo esc_attr__( 'Close floating replay button', 'nelx-product-tour' ); ?>"
				title="<?php echo esc_attr__( 'Close', 'nelx-product-tour' ); ?>"
			>
				×
			</button>
		</div>
		<?php
	}
}
