<?php
/**
 * Plugin Name: Nelx Product Tour
 * Plugin URI: https://nelxstudio.com
 * Description: Build guided product tours directly inside Elementor templates and pages.
 * Version: 1.0.6
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: Nelx Studio
 * Author URI: https://nelxstudio.com
 * Text Domain: nelx-product-tour
 * Domain Path: /languages
 * Elementor tested up to: 3.99
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NELXSTD_PRT_VERSION', '1.0.6' );
define( 'NELXSTD_PRT_FILE', __FILE__ );
define( 'NELXSTD_PRT_PATH', plugin_dir_path( __FILE__ ) );
define( 'NELXSTD_PRT_URL', plugin_dir_url( __FILE__ ) );
define( 'NELXSTD_PRT_BASENAME', plugin_basename( __FILE__ ) );
define( 'NELXSTD_PRT_TEXT_DOMAIN', 'nelx-product-tour' );

require_once NELXSTD_PRT_PATH . 'includes/class-plugin.php';

/**
 * Returns the main plugin instance.
 *
 * @return NELXSTD_PRT_Plugin
 */
function nelxstd_prt() {
	return NELXSTD_PRT_Plugin::instance();
}

nelxstd_prt()->boot();
