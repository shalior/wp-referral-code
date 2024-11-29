<?php

/**
 * Fill WordPress users table with referral stats.
 */
class WP_Referral_Code_Users_Columns {

	private static $instance;

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		global $wp_referral_code_options;

		if ( empty($wp_referral_code_options['show_referral_info_columns'] ) || $wp_referral_code_options['show_referral_info_columns'] === '0') {
			return;
		}

		add_action( 'load-users.php', array( $this, 'load_users_table' ) );
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load_users_table() {
		add_filter( 'manage_users_columns', array( $this, 'add_invite_count_col' ), 100, 1 );
		add_filter( 'manage_users_columns', array( $this, 'add_invited_by_col' ), 100, 1 );

		add_filter( 'manage_users_custom_column', array( $this, 'fill_cells' ), 100, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this, 'register_invite_count_as_sortable' ) );
		add_action( 'pre_get_users', array( $this, 'sort_by_invite_count' ) );
	}

	public function add_invited_by_col( $cols ) {
		$cols['invited_by'] = 'Referred by';

		return $cols;
	}

	public function add_invite_count_col( $cols ) {
		$cols['invited_users_count'] = 'Invited users count';

		return $cols;
	}

	public function fill_cells( $value, $col_name, $user_id ) {
		if ( 'invited_users_count' === $col_name ) {
			$ref_code = new WP_Refer_Code( $user_id );

			return esc_html( count( $ref_code->get_invited_users_id() ) );
		}

		if ( 'invited_by' === $col_name ) {

			$referrer_id = ( new WP_Refer_Code( $user_id ) )->get_referrer_id();
			if ( empty( $referrer_id ) ) {
				return '';
			}

			$referrer_username = esc_html( get_userdata( $referrer_id )->user_login );
			if ( current_user_can( 'edit_user', $referrer_id ) ) {
				$edit_link = esc_url( add_query_arg( 'wp_http_referer', rawurlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), get_edit_user_link( $referrer_id ) ) );
				$edit      = "<strong><a href=\"{$edit_link}\">{$referrer_username}</a></strong>";
			} else {

				$edit = "<strong>{$referrer_username}</strong>";
			}

			return $edit;
		}

		return $value;
	}

	public function register_invite_count_as_sortable( $sortable_columns ) {
		$sortable_columns['invited_users_count'] = 'invited_users_count';
		return $sortable_columns;
	}

	public function sort_by_invite_count( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( 'invited_users_count' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', 'invited_users_count' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}
}

WP_Referral_Code_Users_Columns::get_instance();
