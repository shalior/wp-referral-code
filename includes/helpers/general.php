<?php
// If this file is called directly, abort.
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
 * returns ref query holder: default is 'ref
 * url example: https://domain.com/register/?=ref=324rf4
 * @return string
 */
function wrc_get_ref_code_query() {
	return apply_filters( 'wp_referral_code', 'ref' );
}


/**
 * sets ref code for all users. if refresh is true all user will get new code
 * if refresh if false only users will get a ref code who do not have one already
 *
 * @param bool $refresh
 *
 * @param int $length
 *
 * @return void
 */
function wrc_set_ref_code_all_users( $refresh = false, $length ) {

	$users = get_users();

	foreach ( $users as $user ) {
		$user_id = $user->ID;
		if ( $refresh ) {
			$ref_code = Shalior_Referral_Code_Generator::getInstance()->get_ref_code( $length );
			update_user_meta( $user_id, 'wrc_ref_code', $ref_code );
		} else {
			if ( ! metadata_exists( 'user', $user_id, 'wrc_ref_code' ) ) {
				$ref_code = Shalior_Referral_Code_Generator::getInstance()->get_ref_code( $length );
				update_user_meta( $user_id, 'wrc_ref_code', $ref_code );
			}
		}

	}
}

function wp_referral_code_add_user_to_referrer_invite_list( $user_id, $referrer_id ) {
	$users_referred_by_referrer = get_user_meta( $referrer_id, 'wrc_invited_users', true );
	if ( empty( $users_referred_by_referrer ) ) {
		update_user_meta( $referrer_id, 'wrc_invited_users', [ $user_id ] );
	} else {
		$users_referred_by_referrer[] = $user_id;
		$users_referred_by_referrer   = array_unique( $users_referred_by_referrer );
		update_user_meta( $referrer_id, 'wrc_invited_users', $users_referred_by_referrer );
	}
}

if ( ! function_exists( 'wrc_set_cookie' ) ) {

	function wrc_set_cookie( $name, $value, $expire = 0 ) {
		// todo: test on https and http
		if ( ! headers_sent() ) {
			setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		} elseif ( true === WP_DEBUG ) {
			trigger_error( "{$name} cookie cannot be set headers already sent.", E_USER_NOTICE );
		}
	}

}

if ( ! function_exists( 'write_log' ) ) {

	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}

}