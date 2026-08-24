<?php
/**
 * Main plugin bootstrap.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Nelx Product Tour plugin class.
 */
final class NELXSTD_PRT_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var NELXSTD_PRT_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return NELXSTD_PRT_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Starts plugin hooks.
	 *
	 * @return void
	 */
	public function boot() {
		$this->load_files();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'maybe_boot_elementor' ), 20 );
		add_action( 'admin_notices', array( $this, 'render_missing_elementor_notice' ) );

		NELXSTD_PRT_Assets::instance()->boot();
		NELXSTD_PRT_Progress::instance()->boot();
	}

	/**
	 * Loads plugin classes.
	 *
	 * @return void
	 */
	private function load_files() {
		require_once NELXSTD_PRT_PATH . 'includes/class-assets.php';
		require_once NELXSTD_PRT_PATH . 'includes/class-progress.php';
		require_once NELXSTD_PRT_PATH . 'includes/class-elementor.php';
	}

	/**
	 * Loads translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			NELXSTD_PRT_TEXT_DOMAIN,
			false,
			dirname( NELXSTD_PRT_BASENAME ) . '/languages'
		);
	}

	/**
	 * Boots Elementor integration after Elementor is available.
	 *
	 * @return void
	 */
	public function maybe_boot_elementor() {
		if ( did_action( 'elementor/loaded' ) ) {
			NELXSTD_PRT_Elementor::instance()->boot();
		}
	}

	/**
	 * Shows an admin notice when Elementor is not active.
	 *
	 * @return void
	 */
	public function render_missing_elementor_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || did_action( 'elementor/loaded' ) ) {
			return;
		}

		?>
		<div class="notice notice-warning">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: plugin name */
						__( '%s needs Elementor to create and render tour steps.', 'nelx-product-tour' ),
						'<strong>Nelx Product Tour</strong>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}
}
