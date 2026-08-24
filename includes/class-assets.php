<?php
/**
 * Frontend asset registration.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles scripts, styles, and localized runtime settings.
 */
final class NELXSTD_PRT_Assets {
	/**
	 * Singleton instance.
	 *
	 * @var NELXSTD_PRT_Assets|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return NELXSTD_PRT_Assets
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Starts asset hooks.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Enqueues the product tour runtime for logged-in users and Elementor editing.
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		if ( ! $this->should_enqueue() ) {
			return;
		}

		$driver_js_path  = NELXSTD_PRT_PATH . 'assets/vendor/driver-js/driver.js.iife.js';
		$driver_css_path = NELXSTD_PRT_PATH . 'assets/vendor/driver-js/driver.css';
		$frontend_path   = NELXSTD_PRT_PATH . 'assets/js/nelxstd-prt-frontend.js';
		$style_path      = NELXSTD_PRT_PATH . 'assets/css/nelxstd-prt-frontend.css';

		wp_enqueue_style(
			'nelxstd-prt-driver',
			NELXSTD_PRT_URL . 'assets/vendor/driver-js/driver.css',
			array(),
			file_exists( $driver_css_path ) ? filemtime( $driver_css_path ) : NELXSTD_PRT_VERSION
		);

		wp_enqueue_style(
			'nelxstd-prt-frontend',
			NELXSTD_PRT_URL . 'assets/css/nelxstd-prt-frontend.css',
			array( 'nelxstd-prt-driver' ),
			file_exists( $style_path ) ? filemtime( $style_path ) : NELXSTD_PRT_VERSION
		);

		wp_enqueue_script(
			'nelxstd-prt-driver',
			NELXSTD_PRT_URL . 'assets/vendor/driver-js/driver.js.iife.js',
			array(),
			file_exists( $driver_js_path ) ? filemtime( $driver_js_path ) : NELXSTD_PRT_VERSION,
			true
		);

		wp_enqueue_script(
			'nelxstd-prt-frontend',
			NELXSTD_PRT_URL . 'assets/js/nelxstd-prt-frontend.js',
			array( 'nelxstd-prt-driver' ),
			file_exists( $frontend_path ) ? filemtime( $frontend_path ) : NELXSTD_PRT_VERSION,
			true
		);

		wp_localize_script( 'nelxstd-prt-frontend', 'NELXSTD_PRT_CONFIG', $this->get_runtime_config() );
	}

	/**
	 * Determines whether tour assets should load.
	 *
	 * @return bool
	 */
	private function should_enqueue() {
		return is_user_logged_in() || $this->is_elementor_edit_mode();
	}

	/**
	 * Builds runtime data for JavaScript.
	 *
	 * @return array
	 */
	private function get_runtime_config() {
		$user_id = get_current_user_id();

		return array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'nelxstd_prt_nonce' ),
			'isLoggedIn'    => is_user_logged_in(),
			'isEditor'      => $this->is_elementor_edit_mode(),
			'pageId'        => (int) get_queried_object_id(),
			'userId'        => (int) $user_id,
			'completedKeys' => $user_id ? NELXSTD_PRT_Progress::get_completed_keys( $user_id ) : array(),
			'colors'        => array(
				'primary' => '#1e3a8a',
				'accent'  => '#f4511e',
				'cyan'    => '#00b7c2',
				'dark'    => '#272626',
			),
			'i18n'          => array(
				'takeTour'     => __( 'Take tour', 'nelx-product-tour' ),
				'replayTour'   => __( 'Replay tour', 'nelx-product-tour' ),
				'previewTour'  => __( 'Preview tour', 'nelx-product-tour' ),
				'next'         => __( 'Next', 'nelx-product-tour' ),
				'back'         => __( 'Back', 'nelx-product-tour' ),
				'skip'         => __( 'Skip', 'nelx-product-tour' ),
				'done'         => __( 'Done', 'nelx-product-tour' ),
				'close'        => __( 'Close', 'nelx-product-tour' ),
				'stepProgress' => __( 'Step %1$s of %2$s', 'nelx-product-tour' ),
			),
		);
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
