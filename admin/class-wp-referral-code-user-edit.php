<?php
/**
 * Handles custom functionality on the edit user screen,
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
	 * @since  2.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			return;
		}
		// Only run our customization on the 'user-edit.php' page in the admin.
		add_action( 'load-user-edit.php', array( $this, 'load_user_edit' ) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Adds actions/filters on load.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function load_user_edit() {
		add_action( 'show_user_profile', array( $this, 'profile_fields' ), 1 );
		add_action( 'edit_user_profile', array( $this, 'profile_fields' ), 1 );

	}

	/**
	 * Adds custom profile fields.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param WP_User $user
	 *
	 * @return void
	 */
	public function profile_fields( $user ) {

		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}
		$ref_code = new WP_Refer_Code( $user->ID );
		ob_start();
		include_once 'partials/wp-referral-code-admin-invited-users.php';
		echo ob_get_clean();

	}
}

Shalior_Grs_User_Edit::get_instance();
