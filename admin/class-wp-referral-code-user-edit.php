<?php
/**
 * Handles custom functionality on the edit user screen,
 *
 * @package    WP Referral Code
 * @subpackage Admin
 * @author Shalior<contact@shalior.ir>
 */

/**
 * Edit user screen class.
 *
 * @access public
 */
final class Shalior_Grs_User_Edit {

	private static $instance;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @return void
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			return;
		}
		// Only run our customization on the 'user-edit.php' page in the admin.
		add_action( 'load-user-edit.php', array( $this, 'load_user_edit' ) );
		add_action( 'wp_ajax_wp_referral_code_delete_user_relation', array( $this, 'ajax_delete_user_relation' ) );
	}

	/**
	 * Adds actions/filters on load.
	 *
	 * @return void
	 * @since  1.0.0
	 * @access public
	 */
	public function load_user_edit() {
		add_action( 'show_user_profile', array( $this, 'profile_fields' ), 1 );
		add_action( 'edit_user_profile', array( $this, 'profile_fields' ), 1 );

		add_action( 'profile_update', array( $this, 'update_ref_code' ), 10, 1 );
		add_action( 'user_profile_update_errors', array( $this, 'validate_ref_code' ), 3, 10 );

		wp_enqueue_script( 'wp-referral-code-main', WP_REFERRAL_CODE_URI . '/admin/js/main.min.js', array(), WP_REFERRAL_CODE_VERSION, false );
		wp_localize_script(
			'wp-referral-code-main',
			'WPReferralCode',
			array(
				'alert'          => array(
					'title'       => __( 'Are you sure?', 'wp-referral-code' ),
					'text'        => __( 'You won\'t be able to revert this!', 'wp-referral-code' ),
					'confirmText' => __( 'Yes, delete it!', 'wp-referral-code' ),
					'cancelText'  => __( 'Cancel', 'wp-referral-code' ),
				),
				'confirmedAlert' => array(
					'title' => __( 'Deleted!', 'wp-referral-code' ),
					'text'  => __( 'The relation has been deleted.', 'wp-referral-code' ),
				),
				'nonce'          => wp_create_nonce( 'wp_referral_code_delete_user_relation_nonce' ),
			)
		);

	}

	public function update_ref_code( $user_id ) {

		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( ! isset( $_POST['wrc_update_ref_code_nonce'] ) || ! wp_verify_nonce( $_POST['wrc_update_ref_code_nonce'], 'update_ref_code' ) ) {
			return;
		}

		$new_ref_code = sanitize_text_field( wp_unslash( $_POST['wrc_new_ref_code'] ) );

		if ( empty( $new_ref_code ) ) {
			return;
		}

		( new WP_Refer_Code( $user_id ) )->update_ref_code( $new_ref_code );

	}

	/**
	 * Validate submitted ref code
	 *
	 * @param WP_Error $errors
	 * @param boolean  $update
	 * @param WP_User  $user
	 *
	 * @return void
	 */
	public function validate_ref_code( $errors, $update, $user ) {
		if ( $update ) {
			if ( ! isset( $_POST['wrc_update_ref_code_nonce'] ) || ! wp_verify_nonce( $_POST['wrc_update_ref_code_nonce'], 'update_ref_code' ) ) {
				return;
			}

			$new_ref_code = sanitize_text_field( wp_unslash( $_POST['wrc_new_ref_code'] ) );

			if ( empty( $new_ref_code ) ) {
				return;
			}

			$ref_code_owner_id = WP_Refer_Code::get_user_id_by_ref_code( $new_ref_code );
			// ref code is not already in use.
			if ( false === $ref_code_owner_id ) {
				return;
			}

			if ( (int) $ref_code_owner_id !== $user->ID ) {
				$errors->add( 'unique-ref-code', __( 'Submitted refer code is already in use', 'wp-referral-code' ) );
			}
		}
	}

	/**
	 * Handles ajax request to delete referral relation
	 *
	 * @return void
	 */
	public function ajax_delete_user_relation() {
		$data              = wp_unslash( $_POST );
		$to_delete_user_id = sanitize_text_field( $data['user_id'] );
		$referrer_id       = sanitize_text_field( $data['referrer_id'] );

		if ( ! is_numeric( $to_delete_user_id ) || ! ( new WP_User( $to_delete_user_id ) ) || ! ( new WP_User( $referrer_id ) ) ) {
			wp_send_json_error(
				array(
					'Invalid data',
					$referrer_id,
					$to_delete_user_id,
				),
				422
			);
		}

		check_ajax_referer( 'wp_referral_code_delete_user_relation_nonce', 'nonce', true );

		wp_referral_code_delete_relation( $to_delete_user_id, $referrer_id );
		wp_send_json_success(
			array(
				'done',
				$referrer_id,
				$to_delete_user_id,
			)
		);

	}

	/**
	 * Adds custom profile fields.
	 *
	 * @param WP_User $user
	 *
	 * @return void
	 * @since  1.0.0
	 * @access public
	 */
	public function profile_fields( $user ) {

		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		$user_id  = $user->ID;
		$ref_code = new WP_Refer_Code( $user_id );

		ob_start();
		include_once WP_REFERRAL_CODE_PATH . '/admin/partials/wp-referral-code-admin-invited-users.php';
		include_once WP_REFERRAL_CODE_PATH . '/admin/partials/wp-referral-code-admin-update-ref-code.php';
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
		// phpcs:enable
	}

	/**
	 * Returns the instance.
	 *
	 * @return object
	 * @since  1.0.0
	 * @access public
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Shalior_Grs_User_Edit::get_instance();
