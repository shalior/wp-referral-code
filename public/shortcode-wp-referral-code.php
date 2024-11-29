<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
/**
 * Shortcode for showing referral system related data.
 *
 * @return string
 */
function wp_referral_code_user_param_shortcodes_init() {
	function wp_referral_code_user_param( $atts = array() ) {
		global $wpdb;
		// nothing for not logged in users.
		if ( ! is_user_logged_in() ) {
			return '';
		}
		// analyze shortcode parameters.
		$para     = $atts['var'];
		$user_id  = get_current_user_id();
		$ref_code = new WP_Refer_Code( $user_id );

		switch ( $para ) {
			case 'ref_code':// [wp-referral-code var="ref_code"]
				return $ref_code->get_ref_code();
			case 'ref_link':// [wp-referral-code var="ref_link"]
				return $ref_code->get_ref_link();
			case 'invited_count':// [wp-referral-code var="invited_count"]
				return empty( $ref_code->get_invited_users_id() ) ? '0' : count( $ref_code->get_invited_users_id() );
			case 'most_referring_users': // [wp-referral-code var="most_referring_users"]
				$limit = apply_filters( 'wp_referral_code_invited_limit_most_referring', 10 );

				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT COUNT(meta_value) as counted, meta_value as id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value IS NOT NULL GROUP BY meta_value ORDER BY counted DESC LIMIT %d",
						'wrc_referrer_id',
						intval( $limit )
					),
					ARRAY_A
				);

				ob_start();
				require WP_REFERRAL_CODE_PATH . 'public/partials/most-referring-list.php';

				return ob_get_clean();

			case 'invited_list': // [wp-referral-code var="invited_list"]
				$invited_users = $ref_code->get_invited_users_id();
				ob_start();
				require WP_REFERRAL_CODE_PATH . 'public/partials/invited-list.php';

				return ob_get_clean();
			case 'copy_ref_link':// [wp-referral-code var="copy_ref_link"]
				if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
					// Enqueue jquery only if not enqueued before.
					wp_enqueue_script( 'jquery' );
				}
				if ( ! wp_script_is( 'clipboard', 'enqueued' ) ) {
					// Enqueue clipboard only if not enqueued before.
					wp_enqueue_script( 'clipboard' );
				}
				wp_enqueue_script( 'wrc-copy-ref-link', plugin_dir_url( __FILE__ ) . 'js/wp-referral-code-public.js', array(), WP_REFERRAL_CODE_VERSION, true );
				wp_enqueue_style( 'wrc-copy-ref-link-styles', plugin_dir_url( __FILE__ ) . 'css/wp-referral-code-copy-link.css', array(), WP_REFERRAL_CODE_VERSION );
				ob_start();
				require WP_REFERRAL_CODE_PATH . 'public/partials/copy-ref-link-box.php';

				return ob_get_clean();
		}

		// [wp-referral-code var="valid_invited_count"]
		return '';
	}

	add_shortcode( 'wp-referral-code', 'wp_referral_code_user_param' );
}

add_action( 'init', 'wp_referral_code_user_param_shortcodes_init' );
