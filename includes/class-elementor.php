<?php
/**
 * Elementor control integration.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds product tour controls to Elementor elements.
 */
final class NELXSTD_PRT_Elementor {
	/**
	 * Singleton instance.
	 *
	 * @var NELXSTD_PRT_Elementor|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return NELXSTD_PRT_Elementor
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Starts Elementor hooks.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Generic hook keeps existing widget/section/column support intact.
		add_action( 'elementor/element/after_section_end', array( $this, 'register_tour_controls' ), 10, 3 );


		add_action( 'elementor/frontend/before_render', array( $this, 'add_tour_render_attributes' ) );
	}

	/**
	 * Registers the Elementor category for this plugin.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'nelx-product-tour',
			array(
				'title' => __( 'Nelx Product Tour', 'nelx-product-tour' ),
				'icon'  => 'fa fa-map-signs',
			)
		);
	}

	/**
	 * Registers visible Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		require_once NELXSTD_PRT_PATH . 'includes/class-tour-launcher-widget.php';

		if ( ! class_exists( 'NELXSTD_PRT_Tour_Launcher_Widget' ) ) {
			return;
		}

		$widgets_manager->register( new NELXSTD_PRT_Tour_Launcher_Widget() );
	}

	/**
	 * Dispatches product tour control registration for the current Elementor section.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $section_id Elementor section ID.
	 * @param array                   $args Hook args.
	 * @return void
	 */
	public function register_tour_controls( $element, $section_id, $args ) {
		unset( $args );

		if ( $this->should_register_advanced_controls( $element, $section_id ) ) {
			$this->register_tour_advanced_controls( $element );
		}

		if ( $this->should_register_style_controls( $element, $section_id ) ) {
			$this->register_tour_style_section( $element );
		}
	}

	/**
	 * Registers the product tour setup controls in the Advanced tab.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @return void
	 */
	private function register_tour_advanced_controls( $element ) {
		$existing_controls = method_exists( $element, 'get_controls' ) ? $element->get_controls() : array();
		if ( isset( $existing_controls['nelxstd_prt_enable_step'] ) ) {
			return;
		}

		$element->start_controls_section(
			'nelxstd_prt_section',
			array(
				'label' => __( 'Nelx Product Tour', 'nelx-product-tour' ),
				'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'nelxstd_prt_enable_step',
			array(
				'label'        => __( 'Enable Tour Step', 'nelx-product-tour' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'nelx-product-tour' ),
				'label_off'    => __( 'No', 'nelx-product-tour' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Turn this Elementor element into a guided tour step. Styling controls for the tour appear in this element\'s Style tab when enabled.', 'nelx-product-tour' ),
				'render_type'  => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_tour_id',
			array(
				'label'       => __( 'Tour ID', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'default',
				'placeholder' => 'default',
				'description' => __( 'Use the same Tour ID for steps that belong to one tour.', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_step_order',
			array(
				'label'       => __( 'Step Order', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => 0,
				'step'        => 1,
				'description' => __( 'Lower numbers appear first. Matching numbers fall back to visual top-to-bottom order.', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_step_title',
			array(
				'label'       => __( 'Step Title', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Tour step', 'nelx-product-tour' ),
				'placeholder' => __( 'Example: Your dashboard summary', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_step_description',
			array(
				'label'       => __( 'Step Description', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => __( 'Explain what this part of the page does and what the user should do next.', 'nelx-product-tour' ),
				'placeholder' => __( 'Short, friendly guidance works best.', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_target_mode',
			array(
				'label'       => __( 'Target Mode', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'element',
				'options'     => array(
					'element'  => __( 'This Elementor Element', 'nelx-product-tour' ),
					'selector' => __( 'Custom CSS Selector', 'nelx-product-tour' ),
				),
				'description' => __( 'Use a selector when the visual target is inside the widget or has a custom class/ID.', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_target_selector',
			array(
				'label'       => __( 'CSS Selector', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => '.dashboard-card, #my-task-widget',
				'description' => __( 'Target an Elementor widget ID, custom CSS ID, or class.', 'nelx-product-tour' ),
				'condition'   => array(
					'nelxstd_prt_enable_step' => 'yes',
					'nelxstd_prt_target_mode' => 'selector',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_placement',
			array(
				'label'     => __( 'Tooltip Placement', 'nelx-product-tour' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => array(
					'auto'   => __( 'Auto', 'nelx-product-tour' ),
					'top'    => __( 'Top', 'nelx-product-tour' ),
					'right'  => __( 'Right', 'nelx-product-tour' ),
					'bottom' => __( 'Bottom', 'nelx-product-tour' ),
					'left'   => __( 'Left', 'nelx-product-tour' ),
				),
				'condition' => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_style_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>Tour styling:</strong> Use this element\'s <strong>Style</strong> tab to style the tour card, title, description, step indicator, buttons, target highlight, and replay button.', 'nelx-product-tour' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type'     => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Frontend tours auto-start once per logged-in user and browser. The floating replay button can be dismissed; users can replay the tour from a Product Tour Launcher widget placed on the page.', 'nelx-product-tour' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'nelxstd_prt_enable_step' => 'yes',
				),
				'render_type'     => 'none',
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Adds data attributes consumed by the frontend tour runtime.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @return void
	 */
	public function add_tour_render_attributes( $element ) {
		if ( ! is_user_logged_in() && ! $this->is_elementor_edit_mode() ) {
			return;
		}

		$settings = $element->get_settings_for_display();
		if ( empty( $settings['nelxstd_prt_enable_step'] ) || 'yes' !== $settings['nelxstd_prt_enable_step'] ) {
			return;
		}

		$tour_id     = isset( $settings['nelxstd_prt_tour_id'] ) ? sanitize_key( $settings['nelxstd_prt_tour_id'] ) : 'default';
		$title       = isset( $settings['nelxstd_prt_step_title'] ) ? sanitize_text_field( $settings['nelxstd_prt_step_title'] ) : '';
		$description = isset( $settings['nelxstd_prt_step_description'] ) ? wp_kses_post( $settings['nelxstd_prt_step_description'] ) : '';
		$order       = isset( $settings['nelxstd_prt_step_order'] ) ? absint( $settings['nelxstd_prt_step_order'] ) : 10;
		$mode        = isset( $settings['nelxstd_prt_target_mode'] ) && 'selector' === $settings['nelxstd_prt_target_mode'] ? 'selector' : 'element';
		$selector    = isset( $settings['nelxstd_prt_target_selector'] ) ? sanitize_text_field( $settings['nelxstd_prt_target_selector'] ) : '';
		$placement   = isset( $settings['nelxstd_prt_placement'] ) ? sanitize_key( $settings['nelxstd_prt_placement'] ) : 'auto';
		$show_preview = isset( $settings['nelxstd_prt_show_card_preview'] ) && 'yes' === $settings['nelxstd_prt_show_card_preview'] ? '1' : '0';
		$styles      = $this->build_tour_styles( $settings );

		if ( '' === $tour_id ) {
			$tour_id = 'default';
		}

		$element->add_render_attribute(
			'_wrapper',
			array(
				'class'                               => 'nelxstd-prt-step-source',
				'data-nelxstd-prt-step'              => '1',
				'data-nelxstd-prt-tour-id'           => $tour_id,
				'data-nelxstd-prt-element-id'        => $element->get_id(),
				'data-nelxstd-prt-title'             => $title,
				'data-nelxstd-prt-description'       => $description,
				'data-nelxstd-prt-order'              => $order,
				'data-nelxstd-prt-target-mode'       => $mode,
				'data-nelxstd-prt-target-selector'   => $selector,
				'data-nelxstd-prt-tooltip-placement' => $placement,
				'data-nelxstd-prt-editor-preview'     => $show_preview,
				'data-nelxstd-prt-styles'            => wp_json_encode( $styles ),
			)
		);
	}

	/**
	 * Registers product tour styling controls in Elementor's Style tab.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @return void
	 */
	private function register_tour_style_section( $element ) {
		$existing_controls = method_exists( $element, 'get_controls' ) ? $element->get_controls() : array();
		if ( isset( $existing_controls['nelxstd_prt_card_background'] ) ) {
			return;
		}

		$condition = array(
			'nelxstd_prt_enable_step' => 'yes',
		);

		// Do not call get_settings() while Elementor is building the control stack.
		// Elementor may not have initialized the element data yet, which can result in
		// Controls_Stack::sanitize_settings() receiving null and causing a fatal error.
		// Saved values are loaded by Elementor automatically once these controls exist;
		// legacy 1.0.0 values remain available as runtime fallbacks in build_tour_styles().
		$card_width_default       = array( 'size' => 360, 'unit' => 'px' );
		$card_padding_default     = array( 'top' => 22, 'right' => 22, 'bottom' => 22, 'left' => 22, 'unit' => 'px', 'isLinked' => true );
		$card_radius_default      = array( 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true );
		$highlight_radius_default = array( 'top' => 14, 'right' => 14, 'bottom' => 14, 'left' => 14, 'unit' => 'px', 'isLinked' => true );

		$element->start_controls_section(
			'nelxstd_prt_style_section',
			array(
				'label'     => __( 'Nelx Product Tour', 'nelx-product-tour' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->register_legacy_style_controls( $element );

		$element->add_control(
			'nelxstd_prt_style_intro',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'These controls style the guided tour created by this element. Changes apply to the tour card, its contents, the highlighted target, and the floating replay button.', 'nelx-product-tour' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type'     => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_show_card_preview',
			array(
				'label'        => __( 'Show Tour Card Preview', 'nelx-product-tour' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'nelx-product-tour' ),
				'label_off'    => __( 'Hide', 'nelx-product-tour' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Editor-only preview. Turn this on to display a live tour card while styling this step. It never appears on the public frontend.', 'nelx-product-tour' ),
				'condition'    => $condition,
				'render_type'  => 'none',
			)
		);

		// Tour card.
		$element->add_control(
			'nelxstd_prt_style_heading_card',
			array(
				'label'      => __( 'Tour Card', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$this->add_color_control( $element, 'nelxstd_prt_card_background', __( 'Background', 'nelx-product-tour' ), '#ffffff', $condition );
		$this->add_border_group( $element, 'nelxstd_prt_card_border', __( 'Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_card_shadow', __( 'Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_card_width_control', __( 'Width', 'nelx-product-tour' ), 360, 260, 640, $condition, $card_width_default );
		$this->add_dimensions_control( $element, 'nelxstd_prt_card_padding_control', __( 'Padding', 'nelx-product-tour' ), $card_padding_default, array( 'px', 'em', 'rem' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_card_radius_control', __( 'Border Radius', 'nelx-product-tour' ), $card_radius_default, array( 'px', '%' ), $condition );

		// Text.
		$element->add_control(
			'nelxstd_prt_style_heading_text',
			array(
				'label'      => __( 'Title & Description', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$this->add_color_control( $element, 'nelxstd_prt_title_color', __( 'Title Color', 'nelx-product-tour' ), '#272626', $condition );
		$this->add_typography_group( $element, 'nelxstd_prt_title_typography', __( 'Title Typography', 'nelx-product-tour' ), $condition );
		$this->add_color_control( $element, 'nelxstd_prt_description_color', __( 'Description Color', 'nelx-product-tour' ), '#626262', $condition );
		$this->add_typography_group( $element, 'nelxstd_prt_description_typography', __( 'Description Typography', 'nelx-product-tour' ), $condition );

		// Step indicator.
		$element->add_control(
			'nelxstd_prt_style_heading_indicator',
			array(
				'label'      => __( 'Step Indicator', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$this->add_color_control( $element, 'nelxstd_prt_indicator_background', __( 'Background', 'nelx-product-tour' ), '#e0f7fa', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_indicator_color', __( 'Text Color', 'nelx-product-tour' ), '#1e3a8a', $condition );
		$this->add_typography_group( $element, 'nelxstd_prt_indicator_typography', __( 'Typography', 'nelx-product-tour' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_indicator_padding_control', __( 'Padding', 'nelx-product-tour' ), array( 'top' => 5, 'right' => 10, 'bottom' => 5, 'left' => 10, 'unit' => 'px', 'isLinked' => true ), array( 'px', 'em', 'rem' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_indicator_radius_control', __( 'Border Radius', 'nelx-product-tour' ), array( 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'unit' => 'px', 'isLinked' => true ), array( 'px', '%' ), $condition );

		// Buttons.
		$element->add_control(
			'nelxstd_prt_style_heading_buttons',
			array(
				'label'      => __( 'Tour Buttons', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_navigation_position',
			array(
				'label'       => __( 'Navigation Position', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'inside',
				'options'     => array(
					'inside'  => __( 'Inside Card (Default)', 'nelx-product-tour' ),
					'outside' => __( 'Outside Card', 'nelx-product-tour' ),
				),
				'description' => __( 'On mobile, navigation always stays inside the card. Outside Card places Back/Next below the card on desktop.', 'nelx-product-tour' ),
				'condition'   => $condition,
				'render_type' => 'none',
			)
		);

		$this->add_button_style_controls( $element, 'skip', __( 'Skip Button', 'nelx-product-tour' ), '#fff0eb', '#f4511e', $condition );
		$this->add_button_style_controls( $element, 'back', __( 'Back Button', 'nelx-product-tour' ), '#f4f7fb', '#272626', $condition );
		$this->add_button_style_controls( $element, 'next', __( 'Next / Done Button', 'nelx-product-tour' ), '#1e3a8a', '#ffffff', $condition );

		// Target highlight.
		$element->add_control(
			'nelxstd_prt_style_heading_highlight',
			array(
				'label'      => __( 'Target Highlight', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$this->add_border_group( $element, 'nelxstd_prt_highlight_border', __( 'Highlight Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_highlight_shadow', __( 'Highlight Shadow', 'nelx-product-tour' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_highlight_radius_control', __( 'Border Radius', 'nelx-product-tour' ), $highlight_radius_default, array( 'px', '%' ), $condition );

		// Floating replay button.
		$element->add_control(
			'nelxstd_prt_style_heading_floating',
			array(
				'label'      => __( 'Floating Replay Button', 'nelx-product-tour' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'nelxstd_prt_floating_background_type',
			array(
				'label'       => __( 'Background Type', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''        => __( 'Original Gradient (Default)', 'nelx-product-tour' ),
					'classic' => __( 'Classic', 'nelx-product-tour' ),
					'gradient' => __( 'Gradient', 'nelx-product-tour' ),
				),
				'condition'   => $condition,
				'render_type' => 'none',
			)
		);
		$this->add_color_control( $element, 'nelxstd_prt_floating_background', __( 'Background Color (Classic)', 'nelx-product-tour' ), '', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_floating_gradient_color_a', __( 'Gradient Color 1', 'nelx-product-tour' ), '#1e3a8a', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_floating_gradient_color_b', __( 'Gradient Color 2', 'nelx-product-tour' ), '#00b7c2', $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_floating_gradient_angle_control', __( 'Gradient Angle', 'nelx-product-tour' ), 135, 0, 360, $condition, array( 'size' => 135, 'unit' => 'deg' ) );
		$this->add_color_control( $element, 'nelxstd_prt_floating_color', __( 'Text Color', 'nelx-product-tour' ), '#ffffff', $condition );
		$this->add_typography_group( $element, 'nelxstd_prt_floating_typography', __( 'Typography', 'nelx-product-tour' ), $condition );
		$this->add_border_group( $element, 'nelxstd_prt_floating_border', __( 'Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_floating_shadow', __( 'Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_floating_padding_control', __( 'Padding', 'nelx-product-tour' ), array( 'top' => 14, 'right' => 18, 'bottom' => 14, 'left' => 18, 'unit' => 'px', 'isLinked' => false ), array( 'px', 'em', 'rem' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_floating_radius_control', __( 'Border Radius', 'nelx-product-tour' ), array( 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'unit' => 'px', 'isLinked' => true ), array( 'px', '%' ), $condition );

		$element->add_control(
			'nelxstd_prt_floating_style_heading_hover',
			array(
				'label'       => __( 'Hover', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::HEADING,
				'separator'   => 'before',
				'render_type' => 'none',
			)
		);
		$element->add_control(
			'nelxstd_prt_floating_hover_background_type',
			array(
				'label'       => __( 'Hover Background Type', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''        => __( 'Same as Normal', 'nelx-product-tour' ),
					'classic' => __( 'Classic', 'nelx-product-tour' ),
					'gradient' => __( 'Gradient', 'nelx-product-tour' ),
				),
				'condition'   => $condition,
				'render_type' => 'none',
			)
		);
		$this->add_color_control( $element, 'nelxstd_prt_floating_hover_background', __( 'Hover Background Color (Classic)', 'nelx-product-tour' ), '', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_floating_hover_gradient_color_a', __( 'Hover Gradient Color 1', 'nelx-product-tour' ), '#1e3a8a', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_floating_hover_gradient_color_b', __( 'Hover Gradient Color 2', 'nelx-product-tour' ), '#00b7c2', $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_floating_hover_gradient_angle_control', __( 'Hover Gradient Angle', 'nelx-product-tour' ), 135, 0, 360, $condition, array( 'size' => 135, 'unit' => 'deg' ) );
		$this->add_color_control( $element, 'nelxstd_prt_floating_hover_color', __( 'Hover Text Color', 'nelx-product-tour' ), '#ffffff', $condition );
		$this->add_border_group( $element, 'nelxstd_prt_floating_hover_border', __( 'Hover Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_floating_hover_shadow', __( 'Hover Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_floating_hover_lift_control', __( 'Hover Lift', 'nelx-product-tour' ), -1, -10, 10, $condition, array( 'size' => -1, 'unit' => 'px' ) );

		$element->add_control(
			'nelxstd_prt_floating_close_style_heading',
			array(
				'label'       => __( 'Close Button', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::HEADING,
				'separator'   => 'before',
				'render_type' => 'none',
			)
		);
		$this->add_color_control( $element, 'nelxstd_prt_floating_close_background', __( 'Background', 'nelx-product-tour' ), '#ffffff', $condition );
		$this->add_color_control( $element, 'nelxstd_prt_floating_close_color', __( 'Color', 'nelx-product-tour' ), '#1e3a8a', $condition );
		$this->add_border_group( $element, 'nelxstd_prt_floating_close_border', __( 'Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_floating_close_shadow', __( 'Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_floating_close_size_control', __( 'Close Button Size', 'nelx-product-tour' ), 22, 14, 44, $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_floating_close_hover_lift_control', __( 'Close Hover Lift', 'nelx-product-tour' ), 0, -6, 6, $condition, array( 'size' => 0, 'unit' => 'px' ) );

		$element->end_controls_section();
	}

	/**
	 * Keeps legacy 1.0.0 style settings available to the runtime after the visible
	 * controls have moved to native Elementor groups. These controls are hidden from
	 * the editor UI but remain part of Elementor's settings stack for compatibility.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @return void
	 */
	private function register_legacy_style_controls( $element ) {
		$legacy_controls = array(
			'nelxstd_prt_card_width' => array(
				'default' => 360,
			),
			'nelxstd_prt_card_padding' => array(
				'default' => 22,
			),
			'nelxstd_prt_card_radius' => array(
				'default' => 20,
			),
			'nelxstd_prt_card_border_color' => array(
				'default' => '#e5e7eb',
			),
			'nelxstd_prt_card_border_width' => array(
				'default' => 0,
			),
			'nelxstd_prt_card_shadow' => array(
				'default' => '0 24px 70px rgba(15, 23, 42, 0.22)',
			),
			'nelxstd_prt_title_size' => array(
				'default' => 20,
			),
			'nelxstd_prt_description_size' => array(
				'default' => 14,
			),
			'nelxstd_prt_highlight_radius' => array(
				'default' => 14,
			),
			'nelxstd_prt_highlight_color' => array(
				'default' => '#00b7c2',
			),
			'nelxstd_prt_highlight_width' => array(
				'default' => 4,
			),
		);

		foreach ( $legacy_controls as $id => $control ) {
			$element->add_control(
				$id,
				array(
					'type'    => \Elementor\Controls_Manager::HIDDEN,
					'default' => $control['default'],
				)
			);
		}
	}

	/**
	 * Adds a color control used by the runtime style payload.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $id Control ID.
	 * @param string                  $label Control label.
	 * @param string                  $default Default color.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_color_control( $element, $id, $label, $default, $condition ) {
		$element->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => $default,
				'condition'   => $condition,
				// Keep the generated Elementor CSS away from the parent element.
				// The value is consumed by the Product Tour runtime and applied to the
				// Driver.js overlay/card instead.
				'selectors'    => array(
					'.nelxstd-prt-style-runtime-proxy' => '--nelxstd-prt-control-' . sanitize_key( $id ) . ': {{VALUE}};',
				),
				'render_type' => 'none',
			)
		);
	}

	/**
	 * Adds a typography group without binding it to the Elementor element itself.
	 * The values are serialized into the tour step payload and used by Driver.js.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $name Group name.
	 * @param string                  $label Group label.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_typography_group( $element, $name, $label, $condition ) {
		$element->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'          => $name,
				'label'         => $label,
				'condition'     => $condition,
				// These controls only store values for the Product Tour runtime.
				// They must never generate Elementor CSS for the parent widget/container.
				// The tour overlay is rendered by Driver.js outside the Elementor element tree.
				'selector'      => '.nelxstd-prt-style-runtime-proxy',
				'render_type'   => 'none',
				'fields_options' => array(
					'font_size'      => array( 'responsive' => false ),
					'line_height'    => array( 'responsive' => false ),
					'letter_spacing' => array( 'responsive' => false ),
					'word_spacing'   => array( 'responsive' => false ),
				),
			)
		);
	}

	/**
	 * Adds a native Elementor border group.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $name Group name.
	 * @param string                  $label Group label.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_border_group( $element, $name, $label, $condition ) {
		$element->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'        => $name,
				'label'       => $label,
				'condition'   => $condition,
				// Prevent Elementor from applying border styles to the parent widget.
				// Values are consumed by the Product Tour runtime instead.
				'selector'    => '.nelxstd-prt-style-runtime-proxy',
				'render_type' => 'none',
			)
		);
	}

	/**
	 * Adds a native Elementor box-shadow group.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $name Group name.
	 * @param string                  $label Group label.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_box_shadow_group( $element, $name, $label, $condition ) {
		$element->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'        => $name,
				'label'       => $label,
				'condition'   => $condition,
				// Prevent Elementor from applying box-shadow styles to the parent widget.
				// Values are consumed by the Product Tour runtime instead.
				'selector'    => '.nelxstd-prt-style-runtime-proxy',
				'render_type' => 'none',
			)
		);
	}

	/**
	 * Adds a native Elementor dimensions control.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $id Control ID.
	 * @param string                  $label Control label.
	 * @param array                   $default Default dimensions.
	 * @param array                   $units Allowed units.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_dimensions_control( $element, $id, $label, $default, $units, $condition ) {
		$element->add_responsive_control(
			$id,
			array(
				'label'      => $label,
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => $units,
				'default'    => $default,
				'condition'  => $condition,
				'render_type' => 'none',
				'responsive' => false,
			)
		);
	}

	/**
	 * Adds a slider control.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $id Control ID.
	 * @param string                  $label Control label.
	 * @param int                     $default Default value.
	 * @param int                     $min Minimum value.
	 * @param int                     $max Maximum value.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_slider_control( $element, $id, $label, $default, $min, $max, $condition, $control_default = null ) {
		$element->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => array( 'px', '%', 'em', 'rem', 'deg' ),
				'range'       => array(
					'px' => array(
						'min'  => $min,
						'max'  => $max,
						'step' => 1,
					),
					'deg' => array(
						'min'  => $min,
						'max'  => $max,
						'step' => 1,
					),
				),
				'default'     => null !== $control_default ? $control_default : array(
					'size' => $default,
					'unit' => 'px',
				),
				'condition'   => $condition,
				'render_type' => 'none',
			)
		);
	}

	/**
	 * Adds complete style controls for a tour navigation button.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $prefix Style prefix.
	 * @param string                  $label Button label.
	 * @param string                  $background Default background.
	 * @param string                  $color Default text color.
	 * @param array                   $condition Elementor condition.
	 * @return void
	 */
	private function add_button_style_controls( $element, $prefix, $label, $background, $color, $condition ) {
		$element->add_control(
			'nelxstd_prt_' . $prefix . '_style_heading',
			array(
				'label'      => $label,
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'render_type' => 'none',
			)
		);

		$this->add_color_control( $element, 'nelxstd_prt_' . $prefix . '_background', __( 'Background', 'nelx-product-tour' ), $background, $condition );
		$this->add_color_control( $element, 'nelxstd_prt_' . $prefix . '_color', __( 'Text Color', 'nelx-product-tour' ), $color, $condition );
		$this->add_typography_group( $element, 'nelxstd_prt_' . $prefix . '_typography', __( 'Typography', 'nelx-product-tour' ), $condition );
		$this->add_border_group( $element, 'nelxstd_prt_' . $prefix . '_border', __( 'Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_' . $prefix . '_shadow', __( 'Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_' . $prefix . '_padding_control', __( 'Padding', 'nelx-product-tour' ), array( 'top' => 11, 'right' => 16, 'bottom' => 11, 'left' => 16, 'unit' => 'px', 'isLinked' => false ), array( 'px', 'em', 'rem' ), $condition );
		$this->add_dimensions_control( $element, 'nelxstd_prt_' . $prefix . '_radius_control', __( 'Border Radius', 'nelx-product-tour' ), array( 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'unit' => 'px', 'isLinked' => true ), array( 'px', '%' ), $condition );

		$element->add_control(
			'nelxstd_prt_' . $prefix . '_hover_style_heading',
			array(
				'label'       => __( 'Hover', 'nelx-product-tour' ),
				'type'        => \Elementor\Controls_Manager::HEADING,
				'separator'   => 'before',
				'render_type' => 'none',
			)
		);
		$this->add_color_control( $element, 'nelxstd_prt_' . $prefix . '_hover_background', __( 'Hover Background', 'nelx-product-tour' ), $background, $condition );
		$this->add_color_control( $element, 'nelxstd_prt_' . $prefix . '_hover_color', __( 'Hover Text Color', 'nelx-product-tour' ), $color, $condition );
		$this->add_border_group( $element, 'nelxstd_prt_' . $prefix . '_hover_border', __( 'Hover Border', 'nelx-product-tour' ), $condition );
		$this->add_box_shadow_group( $element, 'nelxstd_prt_' . $prefix . '_hover_shadow', __( 'Hover Box Shadow', 'nelx-product-tour' ), $condition );
		$this->add_slider_control( $element, 'nelxstd_prt_' . $prefix . '_hover_lift_control', __( 'Hover Lift', 'nelx-product-tour' ), -1, -10, 10, $condition, array( 'size' => -1, 'unit' => 'px' ) );
	}

	/**
	 * Builds sanitized tour style data for the frontend.
	 *
	 * Existing 1.0.0 style settings are retained as fallbacks so current tours do
	 * not lose their appearance when the new Elementor native controls are used.
	 *
	 * @param array $settings Elementor settings.
	 * @return array
	 */
	private function build_tour_styles( $settings ) {
		$styles = array(
			'card'      => array(),
			'title'     => array(),
			'description' => array(),
			'indicator' => array(),
			'skip'      => array(),
			'back'      => array(),
			'next'      => array(),
			'highlight' => array(),
			'floating'  => array(),
		);

		// Card.
		$styles['card']['background'] = $this->get_color_setting( $settings, 'nelxstd_prt_card_background', '#ffffff' );
		$styles['card']['width']      = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_card_width_control', $this->get_setting( $settings, 'nelxstd_prt_card_width', 360 ) ), 360, 'px', true );
		$styles['card']['padding']    = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_card_padding_control', $this->get_setting( $settings, 'nelxstd_prt_card_padding', 22 ) ), 22, 'px' );
		$styles['card']['radius']     = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_card_radius_control', $this->get_setting( $settings, 'nelxstd_prt_card_radius', 20 ) ), 20, 'px' );
		$styles['card']['border']     = $this->get_border_setting( $settings, 'nelxstd_prt_card_border', '#e5e7eb', 0, 'nelxstd_prt_card_border_color', 'nelxstd_prt_card_border_width' );
		$styles['card']['shadow']     = $this->get_shadow_setting( $settings, 'nelxstd_prt_card_shadow', '0 24px 70px rgba(15, 23, 42, 0.22)' );
		$styles['navigation_position'] = isset( $settings['nelxstd_prt_navigation_position'] ) && 'outside' === $settings['nelxstd_prt_navigation_position'] ? 'outside' : 'inside';

		// Title and description.
		$styles['title'] = $this->get_typography_setting(
			$settings,
			'nelxstd_prt_title_typography',
			'#272626',
			20,
			'nelxstd_prt_title_color',
			'nelxstd_prt_title_size'
		);

		$styles['description'] = $this->get_typography_setting(
			$settings,
			'nelxstd_prt_description_typography',
			'#626262',
			14,
			'nelxstd_prt_description_color',
			'nelxstd_prt_description_size'
		);

		// Step indicator.
		$styles['indicator']['background'] = $this->get_color_setting( $settings, 'nelxstd_prt_indicator_background', '#e0f7fa' );
		$styles['indicator']['color']      = $this->get_color_setting( $settings, 'nelxstd_prt_indicator_color', '#1e3a8a' );
		$styles['indicator']['typography'] = $this->get_typography_setting( $settings, 'nelxstd_prt_indicator_typography', $styles['indicator']['color'], 12, '', '' );
		$styles['indicator']['padding']    = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_indicator_padding_control', 5 ), 5, 'px' );
		$styles['indicator']['radius']     = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_indicator_radius_control', 999 ), 999, 'px' );

		// Buttons. Keep old 1.0.0 values as fallbacks.
		$styles['skip'] = $this->get_button_style( $settings, 'skip', '#fff0eb', '#f4511e' );
		$styles['back'] = $this->get_button_style( $settings, 'back', '#f4f7fb', '#272626' );
		$styles['next'] = $this->get_button_style( $settings, 'next', '#1e3a8a', '#ffffff' );

		// Highlight target. New native border group values take precedence over the legacy controls.
		$styles['highlight']['border'] = $this->get_border_setting( $settings, 'nelxstd_prt_highlight_border', '#00b7c2', 4, 'nelxstd_prt_highlight_color', 'nelxstd_prt_highlight_width' );
		$styles['highlight']['shadow'] = $this->get_shadow_setting( $settings, 'nelxstd_prt_highlight_shadow', '0 18px 55px rgba(30, 58, 138, 0.28)' );
		$styles['highlight']['radius'] = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_highlight_radius_control', $this->get_setting( $settings, 'nelxstd_prt_highlight_radius', 14 ) ), 14, 'px' );

		// Floating replay button.
		$styles['floating']['background'] = $this->get_floating_background( $settings, false );
		$styles['floating']['color']      = $this->get_color_setting( $settings, 'nelxstd_prt_floating_color', '#ffffff' );
		$styles['floating']['typography'] = $this->get_typography_setting( $settings, 'nelxstd_prt_floating_typography', '#ffffff', 14, '', '' );
		$styles['floating']['border']     = $this->get_border_setting( $settings, 'nelxstd_prt_floating_border', 'transparent', 0, '', '' );
		$styles['floating']['shadow']     = $this->get_shadow_setting( $settings, 'nelxstd_prt_floating_shadow', '0 16px 36px rgba(30, 58, 138, 0.28)' );
		$styles['floating']['padding']    = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_floating_padding_control', array( 'top' => 14, 'right' => 18, 'bottom' => 14, 'left' => 18, 'unit' => 'px' ) ), 14, 'px' );
		$styles['floating']['radius']     = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_floating_radius_control', 999 ), 999, 'px' );
		$styles['floating']['hover_background'] = $this->get_floating_background( $settings, true, $styles['floating']['background'] );
		$styles['floating']['hover_color']      = $this->get_color_setting( $settings, 'nelxstd_prt_floating_hover_color', '#ffffff' );
		$styles['floating']['hover_border']    = $this->get_border_setting_with_fallback( $settings, 'nelxstd_prt_floating_hover_border', $styles['floating']['border'] );
		$styles['floating']['hover_shadow']    = $this->get_shadow_setting_with_fallback( $settings, 'nelxstd_prt_floating_hover_shadow', $styles['floating']['shadow'] );
		$styles['floating']['hover_lift']      = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_floating_hover_lift_control', array( 'size' => -1, 'unit' => 'px' ) ), -1, 'px', true );
		$styles['floating']['close_background'] = $this->get_color_setting( $settings, 'nelxstd_prt_floating_close_background', '#ffffff' );
		$styles['floating']['close_color']      = $this->get_color_setting( $settings, 'nelxstd_prt_floating_close_color', '#1e3a8a' );
		$styles['floating']['close_border']    = $this->get_border_setting( $settings, 'nelxstd_prt_floating_close_border', 'transparent', 0, '', '' );
		$styles['floating']['close_shadow']    = $this->get_shadow_setting( $settings, 'nelxstd_prt_floating_close_shadow', '0 5px 16px rgba(15, 23, 42, 0.22)' );
		$styles['floating']['close_size']       = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_floating_close_size_control', 22 ), 22, 'px', true );
		$styles['floating']['close_hover_lift'] = $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_floating_close_hover_lift_control', array( 'size' => 0, 'unit' => 'px' ) ), 0, 'px', true );

		return $styles;
	}

	/**
	 * Builds a button style payload.
	 *
	 * @param array  $settings Elementor settings.
	 * @param string $prefix Button prefix.
	 * @param string $background Default background.
	 * @param string $color Default text color.
	 * @return array
	 */
	private function get_button_style( $settings, $prefix, $background, $color ) {
		$legacy_background = 'skip' === $prefix ? 'nelxstd_prt_skip_background' : ( 'back' === $prefix ? 'nelxstd_prt_back_background' : 'nelxstd_prt_next_background' );
		$legacy_color      = 'skip' === $prefix ? 'nelxstd_prt_skip_color' : ( 'back' === $prefix ? 'nelxstd_prt_back_color' : 'nelxstd_prt_next_color' );
		$normal_border     = $this->get_border_setting( $settings, 'nelxstd_prt_' . $prefix . '_border', 'transparent', 0, '', '' );
		$normal_shadow     = $this->get_shadow_setting( $settings, 'nelxstd_prt_' . $prefix . '_shadow', 'none' );

		return array(
			'background'       => $this->get_color_setting( $settings, 'nelxstd_prt_' . $prefix . '_background', $background, $legacy_background ),
			'color'            => $this->get_color_setting( $settings, 'nelxstd_prt_' . $prefix . '_color', $color, $legacy_color ),
			'typography'       => $this->get_typography_setting( $settings, 'nelxstd_prt_' . $prefix . '_typography', $color, 13, '', '' ),
			'border'           => $normal_border,
			'shadow'           => $normal_shadow,
			'padding'          => $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_' . $prefix . '_padding_control', array( 'top' => 11, 'right' => 16, 'bottom' => 11, 'left' => 16, 'unit' => 'px' ) ), 11, 'px' ),
			'radius'           => $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_' . $prefix . '_radius_control', 999 ), 999, 'px' ),
			'hover_background' => $this->get_color_setting( $settings, 'nelxstd_prt_' . $prefix . '_hover_background', $background ),
			'hover_color'      => $this->get_color_setting( $settings, 'nelxstd_prt_' . $prefix . '_hover_color', $color ),
			'hover_border'     => $this->get_border_setting_with_fallback( $settings, 'nelxstd_prt_' . $prefix . '_hover_border', $normal_border ),
			'hover_shadow'     => $this->get_shadow_setting_with_fallback( $settings, 'nelxstd_prt_' . $prefix . '_hover_shadow', $normal_shadow ),
			'hover_lift'       => $this->get_dimension_setting( $this->get_setting( $settings, 'nelxstd_prt_' . $prefix . '_hover_lift_control', array( 'size' => -1, 'unit' => 'px' ) ), -1, 'px', true ),
		);
	}

	/**
	 * Builds the floating replay button background, preserving legacy color values.
	 *
	 * @param array  $settings Elementor settings.
	 * @param bool   $hover Whether to build the hover background.
	 * @param string $normal_fallback Normal background fallback for hover mode.
	 * @return string
	 */
	private function get_floating_background( $settings, $hover = false, $normal_fallback = '' ) {
		$prefix = $hover ? 'nelxstd_prt_floating_hover' : 'nelxstd_prt_floating';
		$type   = isset( $settings[ $prefix . '_background_type' ] ) ? sanitize_key( $settings[ $prefix . '_background_type' ] ) : '';

		if ( $hover && '' === $type ) {
			return $normal_fallback ? $normal_fallback : 'linear-gradient(135deg, #1e3a8a, #00b7c2)';
		}

		// Preserve a custom 1.0.x floating button color until the user explicitly
		// chooses a new background type. New elements with an empty legacy value
		// receive the original gradient by default.
		if ( ! $hover && '' === $type && ! empty( $settings['nelxstd_prt_floating_background'] ) && '#1e3a8a' !== strtolower( trim( (string) $settings['nelxstd_prt_floating_background'] ) ) ) {
			return sanitize_text_field( $settings['nelxstd_prt_floating_background'] );
		}

		if ( 'classic' === $type ) {
			$legacy_key = $hover ? 'nelxstd_prt_floating_hover_background' : 'nelxstd_prt_floating_background';
			return $this->get_color_setting( $settings, $legacy_key, '#1e3a8a' );
		}

		if ( 'gradient' === $type || '' === $type ) {
			$color_a_key = $prefix . '_gradient_color_a';
			$color_b_key = $prefix . '_gradient_color_b';
			$angle_key   = $prefix . '_gradient_angle_control';
			$color_a    = $this->get_color_setting( $settings, $color_a_key, '#1e3a8a' );
			$color_b    = $this->get_color_setting( $settings, $color_b_key, '#00b7c2' );
			$angle      = $this->get_dimension_setting( $this->get_setting( $settings, $angle_key, array( 'size' => 135, 'unit' => 'deg' ) ), 135, 'deg', true );

			return 'linear-gradient(' . $angle['size'] . 'deg, ' . $color_a . ', ' . $color_b . ')';
		}

		return 'linear-gradient(135deg, #1e3a8a, #00b7c2)';
	}

	/**
	 * Returns a border group, falling back to an already resolved border when no hover values exist.
	 *
	 * @param array  $settings Elementor settings.
	 * @param string $prefix Group prefix.
	 * @param array  $fallback Resolved border payload.
	 * @return array
	 */
	private function get_border_setting_with_fallback( $settings, $prefix, $fallback ) {
		$style = isset( $settings[ $prefix . '_border' ] ) ? $settings[ $prefix . '_border' ] : '';
		$color = isset( $settings[ $prefix . '_color' ] ) ? $settings[ $prefix . '_color' ] : '';
		$width = isset( $settings[ $prefix . '_width' ] ) ? $settings[ $prefix . '_width' ] : array();

		if ( '' === $style && '' === $color && ! $this->has_dimension_value( $width ) ) {
			return $fallback;
		}

		return $this->get_border_setting( $settings, $prefix, $fallback['color'], 0, '', '' );
	}

	/**
	 * Returns a box shadow group, falling back to an already resolved shadow when no hover values exist.
	 *
	 * @param array  $settings Elementor settings.
	 * @param string $prefix Group prefix.
	 * @param mixed  $fallback Resolved shadow payload.
	 * @return mixed
	 */
	private function get_shadow_setting_with_fallback( $settings, $prefix, $fallback ) {
		$value = isset( $settings[ $prefix . '_box_shadow' ] ) ? $settings[ $prefix . '_box_shadow' ] : array();

		if ( ! is_array( $value ) || ( ! isset( $value['horizontal'] ) && ! isset( $value['vertical'] ) && ! isset( $value['blur'] ) && ! isset( $value['spread'] ) && empty( $value['color'] ) && empty( $value['inset'] ) ) ) {
			return $fallback;
		}

		return $this->get_shadow_setting( $settings, $prefix, $fallback );
	}

	/**
	 * Determines whether an Elementor dimensions value contains a user value.
	 *
	 * @param mixed $value Dimension value.
	 * @return bool
	 */
	private function has_dimension_value( $value ) {
		if ( is_array( $value ) && isset( $value['size'] ) ) {
			return '' !== $value['size'] && null !== $value['size'];
		}

		if ( is_array( $value ) ) {
			foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
				if ( isset( $value[ $side ] ) && '' !== $value[ $side ] && null !== $value[ $side ] ) {
					return true;
				}
			}
		}

		return is_numeric( $value );
	}

	/**
	 * Gets a setting value.
	 *
	 * @param array  $settings Settings.
	 * @param string $key Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	private function get_setting( $settings, $key, $default = null ) {
		return isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : $default;
	}

	/**
	 * Gets and sanitizes a color setting.
	 *
	 * @param array  $settings Settings.
	 * @param string $key Primary key.
	 * @param string $default Default color.
	 * @param string $legacy_key Optional legacy key.
	 * @return string
	 */
	private function get_color_setting( $settings, $key, $default, $legacy_key = '' ) {
		$value = isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : '';

		if ( '' === $value && $legacy_key && isset( $settings[ $legacy_key ] ) && '' !== $settings[ $legacy_key ] ) {
			$value = $settings[ $legacy_key ];
		}

		return $value ? sanitize_text_field( $value ) : $default;
	}

	/**
	 * Converts an Elementor dimensions/slider value into a predictable payload.
	 *
	 * @param mixed  $value Elementor value.
	 * @param float  $fallback Fallback numeric value.
	 * @param string $unit Fallback unit.
	 * @param bool   $single Whether this is a one-dimensional value.
	 * @return array
	 */
	private function get_dimension_setting( $value, $fallback, $unit = 'px', $single = false ) {
		if ( is_array( $value ) && isset( $value['size'] ) ) {
			return array(
				'size' => is_numeric( $value['size'] ) ? (float) $value['size'] : (float) $fallback,
				'unit' => ! empty( $value['unit'] ) ? $this->sanitize_css_unit( $value['unit'], $unit ) : $unit,
			);
		}

		if ( $single ) {
			return array(
				'size' => is_numeric( $value ) ? (float) $value : (float) $fallback,
				'unit' => $unit,
			);
		}

		if ( is_array( $value ) ) {
			$top    = isset( $value['top'] ) && is_numeric( $value['top'] ) ? (float) $value['top'] : (float) $fallback;
			$right  = isset( $value['right'] ) && is_numeric( $value['right'] ) ? (float) $value['right'] : $top;
			$bottom = isset( $value['bottom'] ) && is_numeric( $value['bottom'] ) ? (float) $value['bottom'] : $top;
			$left   = isset( $value['left'] ) && is_numeric( $value['left'] ) ? (float) $value['left'] : $right;

			return array(
				'top'    => $top,
				'right'  => $right,
				'bottom' => $bottom,
				'left'   => $left,
				'unit'   => ! empty( $value['unit'] ) ? $this->sanitize_css_unit( $value['unit'], $unit ) : $unit,
			);
		}

		return array(
			'top'    => is_numeric( $value ) ? (float) $value : (float) $fallback,
			'right'  => is_numeric( $value ) ? (float) $value : (float) $fallback,
			'bottom' => is_numeric( $value ) ? (float) $value : (float) $fallback,
			'left'   => is_numeric( $value ) ? (float) $value : (float) $fallback,
			'unit'   => $unit,
		);
	}

	/**
	 * Extracts a native Elementor border group, with legacy fallback support.
	 *
	 * @param array  $settings Settings.
	 * @param string $prefix Group prefix.
	 * @param string $legacy_color Default/legacy color.
	 * @param int    $legacy_width Default/legacy width.
	 * @param string $legacy_color_key Legacy color key.
	 * @param string $legacy_width_key Legacy width key.
	 * @return array
	 */
	private function get_border_setting( $settings, $prefix, $legacy_color, $legacy_width, $legacy_color_key = '', $legacy_width_key = '' ) {
		$style = isset( $settings[ $prefix . '_border' ] ) ? sanitize_key( $settings[ $prefix . '_border' ] ) : '';
		$color = isset( $settings[ $prefix . '_color' ] ) && '' !== $settings[ $prefix . '_color' ] ? sanitize_text_field( $settings[ $prefix . '_color' ] ) : '';
		$width = isset( $settings[ $prefix . '_width' ] ) ? $settings[ $prefix . '_width' ] : null;

		if ( '' === $color && $legacy_color_key ) {
			$color = $this->get_color_setting( $settings, $legacy_color_key, $legacy_color );
		}

		if ( ! $color ) {
			$color = $legacy_color;
		}

		if ( null === $width && $legacy_width_key ) {
			$width = $this->get_setting( $settings, $legacy_width_key, $legacy_width );
		}

		$width = $this->get_dimension_setting( null !== $width ? $width : $legacy_width, $legacy_width, 'px' );

		if ( '' === $style ) {
			$style = ( 0 < max( $width['top'], $width['right'], $width['bottom'], $width['left'] ) ) ? 'solid' : 'solid';
		}

		return array(
			'style'  => $style,
			'color'  => $color,
			'width'  => $width,
		);
	}

	/**
	 * Extracts a native Elementor box-shadow group, preserving legacy string shadows.
	 *
	 * @param array  $settings Settings.
	 * @param string $prefix Group prefix.
	 * @param string $legacy_shadow Legacy shadow string.
	 * @return mixed
	 */
	private function get_shadow_setting( $settings, $prefix, $legacy_shadow ) {
		$value = isset( $settings[ $prefix . '_box_shadow' ] ) ? $settings[ $prefix . '_box_shadow' ] : '';

		if ( is_array( $value ) ) {
			return array(
				'horizontal' => isset( $value['horizontal'] ) ? (float) $value['horizontal'] : 0,
				'vertical'   => isset( $value['vertical'] ) ? (float) $value['vertical'] : 0,
				'blur'       => isset( $value['blur'] ) ? (float) $value['blur'] : 0,
				'spread'     => isset( $value['spread'] ) ? (float) $value['spread'] : 0,
				'color'      => ! empty( $value['color'] ) ? sanitize_text_field( $value['color'] ) : 'rgba(15, 23, 42, 0.22)',
				'inset'      => ! empty( $value['inset'] ),
			);
		}

		return $legacy_shadow;
	}

	/**
	 * Extracts typography settings from an Elementor typography group.
	 *
	 * @param array  $settings Settings.
	 * @param string $prefix Group prefix.
	 * @param string $default_color Default color.
	 * @param int    $default_size Default font size.
	 * @param string $legacy_color_key Legacy color key.
	 * @param string $legacy_size_key Legacy size key.
	 * @return array
	 */
	private function get_typography_setting( $settings, $prefix, $default_color, $default_size, $legacy_color_key = '', $legacy_size_key = '' ) {
		$color = $default_color;
		if ( $legacy_color_key ) {
			$color = $this->get_color_setting( $settings, $legacy_color_key, $default_color );
		}

		$size = $default_size;
		if ( $legacy_size_key ) {
			$size = is_numeric( $this->get_setting( $settings, $legacy_size_key, $default_size ) ) ? (float) $this->get_setting( $settings, $legacy_size_key, $default_size ) : $default_size;
		}

		$typography = array(
			'color'          => $color,
			'font_family'    => isset( $settings[ $prefix . '_font_family' ] ) ? sanitize_text_field( $settings[ $prefix . '_font_family' ] ) : '',
			'font_size'      => $this->get_dimension_setting( isset( $settings[ $prefix . '_font_size' ] ) ? $settings[ $prefix . '_font_size' ] : $size, $size, 'px', true ),
			'font_weight'    => isset( $settings[ $prefix . '_font_weight' ] ) ? sanitize_text_field( $settings[ $prefix . '_font_weight' ] ) : '',
			'font_style'     => isset( $settings[ $prefix . '_font_style' ] ) ? sanitize_key( $settings[ $prefix . '_font_style' ] ) : '',
			'text_transform' => isset( $settings[ $prefix . '_text_transform' ] ) ? sanitize_key( $settings[ $prefix . '_text_transform' ] ) : '',
			'text_decoration'=> isset( $settings[ $prefix . '_text_decoration' ] ) ? sanitize_key( $settings[ $prefix . '_text_decoration' ] ) : '',
			'line_height'    => $this->get_optional_dimension( isset( $settings[ $prefix . '_line_height' ] ) ? $settings[ $prefix . '_line_height' ] : '', 'normal' ),
			'letter_spacing' => $this->get_optional_dimension( isset( $settings[ $prefix . '_letter_spacing' ] ) ? $settings[ $prefix . '_letter_spacing' ] : '', 'normal' ),
			'word_spacing'   => $this->get_optional_dimension( isset( $settings[ $prefix . '_word_spacing' ] ) ? $settings[ $prefix . '_word_spacing' ] : '', 'normal' ),
		);

		return $typography;
	}

	/**
	 * Sanitizes a CSS unit while preserving percentage and other valid Elementor units.
	 *
	 * @param string $unit Unit value.
	 * @param string $fallback Fallback unit.
	 * @return string
	 */
	private function sanitize_css_unit( $unit, $fallback = 'px' ) {
		$allowed = array( 'px', '%', 'em', 'rem', 'vh', 'vw', 'vmin', 'vmax', 'deg' );
		$unit    = strtolower( trim( (string) $unit ) );

		return in_array( $unit, $allowed, true ) ? $unit : $fallback;
	}

	/**
	 * Converts an optional Elementor slider into a CSS-ready value payload.
	 *
	 * @param mixed  $value Elementor value.
	 * @param string $fallback Fallback CSS value.
	 * @return mixed
	 */
	private function get_optional_dimension( $value, $fallback ) {
		if ( is_array( $value ) && isset( $value['size'] ) && '' !== $value['size'] ) {
			return array(
				'size' => is_numeric( $value['size'] ) ? (float) $value['size'] : 0,
				'unit' => ! empty( $value['unit'] ) ? $this->sanitize_css_unit( $value['unit'], '' ) : '',
			);
		}

		return $fallback;
	}

	/**
	 * Determines whether the Advanced tab controls should be injected for this section.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $section_id Section ID.
	 * @return bool
	 */
	private function should_register_advanced_controls( $element, $section_id ) {
		// Keep the original Elementor integration points so existing widget-level
		// elements (Button, Icon, Image, etc.) continue to receive the tour controls.
		// The generic after_section_end hook is used for widgets, sections, columns,
		// and containers; containers additionally expose their layout section here.
		if ( in_array( $section_id, array( '_section_style', 'section_advanced' ), true ) ) {
			return true;
		}

		if ( 'container' === $this->get_element_type( $element ) && in_array( $section_id, array( 'section_layout', '_section_layout' ), true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Determines whether Style tab controls should be injected for this section.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @param string                  $section_id Section ID.
	 * @return bool
	 */
	private function should_register_style_controls( $element, $section_id ) {
		// Register the Style tab section immediately after the Advanced controls are
		// registered. This guarantees the Enable Tour Step control already exists when
		// Elementor evaluates the Style tab conditions. Elementor still places the
		// section under the Style tab regardless of registration order.
		return $this->should_register_advanced_controls( $element, $section_id );
	}

	/**
	 * Gets an Elementor element type safely.
	 *
	 * @param \Elementor\Element_Base $element Elementor element.
	 * @return string
	 */
	private function get_element_type( $element ) {
		return method_exists( $element, 'get_type' ) ? $element->get_type() : '';
	}

	/**
	 * Detects Elementor edit mode.
	 *
	 * @return bool
	 */
	private function is_elementor_edit_mode() {
		return class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->editor )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();
	}
}
