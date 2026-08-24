<?php
/**
 * Tour completion persistence.
 *
 * @package NelxProductTour
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persists completed tours per logged-in WordPress user.
 */
final class NELXSTD_PRT_Progress {
	const META_KEY = 'nelxstd_prt_completed_tours';

	/**
	 * Singleton instance.
	 *
	 * @var NELXSTD_PRT_Progress|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return NELXSTD_PRT_Progress
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Starts AJAX hooks.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'wp_ajax_nelxstd_prt_complete_tour', array( $this, 'ajax_complete_tour' ) );
	}

	/**
	 * Returns completed tour keys for a user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array
	 */
	public static function get_completed_keys( $user_id ) {
		$completed = get_user_meta( $user_id, self::META_KEY, true );

		if ( ! is_array( $completed ) ) {
			return array();
		}

		return array_keys( $completed );
	}

	/**
	 * AJAX endpoint that marks a tour complete for the current user.
	 *
	 * @return void
	 */
	public function ajax_complete_tour() {
		if ( ! check_ajax_referer( 'nelxstd_prt_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'nelx-product-tour' ) ), 403 );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to save tour progress.', 'nelx-product-tour' ) ), 401 );
		}

		$page_id = isset( $_POST['pageId'] ) ? absint( wp_unslash( $_POST['pageId'] ) ) : 0;
		$tour_id = isset( $_POST['tourId'] ) ? sanitize_key( wp_unslash( $_POST['tourId'] ) ) : 'default';
		$key     = self::build_completion_key( $page_id, $tour_id );

		$completed = get_user_meta( get_current_user_id(), self::META_KEY, true );
		if ( ! is_array( $completed ) ) {
			$completed = array();
		}

		$completed[ $key ] = current_time( 'mysql', true );
		update_user_meta( get_current_user_id(), self::META_KEY, $completed );

		wp_send_json_success(
			array(
				'key'       => $key,
				'completed' => $completed[ $key ],
			)
		);
	}

	/**
	 * Builds a stable completion key for a page/template and tour.
	 *
	 * @param int    $page_id Page or template ID.
	 * @param string $tour_id Tour group ID.
	 * @return string
	 */
	public static function build_completion_key( $page_id, $tour_id ) {
		$page_id = absint( $page_id );
		$tour_id = sanitize_key( $tour_id ? $tour_id : 'default' );

		return $page_id . '::' . $tour_id;
	}
}
