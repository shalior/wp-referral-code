<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Simple check for validating a URL, it must start with http:// or https://.
 * and pass FILTER_VALIDATE_URL validation.
 *
 * @param string $url to check.
 *
 * @return bool
 */
function wrc_is_valid_url( $url ) {

	// Must start with http:// or https://.
	if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		return false;
	}

	// Must pass validation.
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	return true;
}

/**
 * Returns ref query holder: default is 'ref
 * url example: https://domain.com/register/?=ref=324rf4
 *
 * @return string
 */
function wrc_get_ref_code_query() {
	return apply_filters( 'wp_referral_code', 'ref' );
}


/**
 * Sets ref code for all users. if refresh is true all user will get new code
 * if refresh false only users will get a ref code who do not have one already
 *
 * @param bool $refresh
 *
 * @param int  $length
 *
 * @return void
 */
function wrc_set_ref_code_all_users( $refresh, $length ) {

	$users = get_users();

	foreach ( $users as $user ) {
		$user_id = $user->ID;
		if ( $refresh ) {
			$ref_code = Shalior_Referral_Code_Generator::get_instance()->get_ref_code( $length );
			update_user_meta( $user_id, 'wrc_ref_code', $ref_code );
		} else {
			if ( ! metadata_exists( 'user', $user_id, 'wrc_ref_code' ) ) {
				$ref_code = Shalior_Referral_Code_Generator::get_instance()->get_ref_code( $length );
				update_user_meta( $user_id, 'wrc_ref_code', $ref_code );
			}
		}
	}
}

function wp_referral_code_add_user_to_referrer_invite_list( $user_id, $referrer_id ) {
	$users_referred_by_referrer = get_user_meta( $referrer_id, 'wrc_invited_users', true );
	if ( empty( $users_referred_by_referrer ) ) {
		update_user_meta( $referrer_id, 'wrc_invited_users', array( $user_id ) );
	} else {
		$users_referred_by_referrer[] = $user_id;
		$users_referred_by_referrer   = array_unique( $users_referred_by_referrer );
		update_user_meta( $referrer_id, 'wrc_invited_users', $users_referred_by_referrer );
	}
}

if ( ! function_exists( 'wp_referral_code_delete_relation' ) ) {
	function wp_referral_code_delete_relation( $to_delete_user_id, $referrer_id ) {
		$users_referred_by_referrer = get_user_meta( $referrer_id, 'wrc_invited_users', true );
		if ( empty( $users_referred_by_referrer ) ) {
			return;
		}
		$users_referred_by_referrer = array_diff( $users_referred_by_referrer, array( $to_delete_user_id ) );
		$users_referred_by_referrer = array_unique( $users_referred_by_referrer );
		update_user_meta( $referrer_id, 'wrc_invited_users', $users_referred_by_referrer );
		update_user_meta( $to_delete_user_id, 'wrc_referrer_id', null );

		do_action( 'wp_referral_code_after_relation_deleted', $to_delete_user_id, $referrer_id );
	}
}


if ( ! function_exists( 'wrc_set_cookie' ) ) {

	function wrc_set_cookie( $name, $value, $expire = 0 ) {
		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		} elseif ( true === WP_DEBUG ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "{$name} cookie cannot be set headers already sent.", E_USER_NOTICE );
			// phpcs:enable
		}
	}
}

