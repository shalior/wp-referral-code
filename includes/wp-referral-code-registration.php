<?php
add_action( 'user_register', 'wp_referral_code_handle_new_registration', 20, 1 );
/**
 * Save referral data of newly registered user
 *
 * @param int $user_id User's id.
 *
 * @return void
 */
function wp_referral_code_handle_new_registration( $user_id ) {
	$ref_code = '';
	if ( isset( $_COOKIE['refer_code'] ) ) {
		$ref_code = sanitize_text_field( wp_unslash( $_COOKIE['refer_code'] ) );
	}

	$ref_code = apply_filters( 'wp_referral_code_new_user_ref_code', $ref_code, $user_id );

	$new_user_id = $user_id;

	// make ref code for new users.
	$new_user_ref_code = new WP_Refer_Code( $new_user_id );

	// out if no ref code.
	if ( empty( $ref_code ) ) {
		return;
	}

	$referrer_user_id = WP_Refer_Code::get_user_id_by_ref_code( $ref_code );

	// if quite if the ref_code is invalid.
	if ( false === $referrer_user_id ) {
		if ( isset( $_COOKIE['refer_code'] ) ) {
			wrc_set_cookie( 'refer_code', 0, time() - HOUR_IN_SECONDS );
			unset( $_COOKIE['refer_code'] );
		}

		return;
	}

	/**
	 * Fires before refer code related information are submitted on database
	 * this action won't run if ref code doesn't exist
	 * passed parameters:
	 * $new_user_id: id of newly registered user
	 * $referrer_user_id: id of the user who referred newly registered user (the guy who should be rewarded :) )
	 * $ref_code: referral code of referrer
	 * $new_user_refer_codeL refer_code of newly registered user
	 */
	do_action( 'wp_referral_code_before_refer_submitted', $new_user_id, $referrer_user_id, $ref_code, $new_user_ref_code );

	if ( ! apply_filters( 'wp_referral_code_validate_submission', true, $new_user_id, $referrer_user_id, $ref_code, $new_user_ref_code ) ) {
		return;
	}

	// set referrer as inviter of new user.
	update_user_meta( $new_user_id, 'wrc_referrer_id', $referrer_user_id );

	// adding new user to referrer invited list.
	wp_referral_code_add_user_to_referrer_invite_list( $new_user_id, $referrer_user_id );
	/**
	 * Fires after refer code related information are submitted on database
	 * this action won't run if ref code doesn't exist
	 * passed parameters:
	 * $new_user_id: id of newly registered user
	 * $referrer_user_id: id of the user who referred newly registered user (the guy who should be rewarded :) )
	 * $ref_code: referral code of referrer
	 * $new_user_refer_codeL refer_code of newly registered user
	 */
	do_action( 'wp_referral_code_after_refer_submitted', $new_user_id, $referrer_user_id, $ref_code, $new_user_ref_code );

	// remove cookie.
	if ( isset( $_COOKIE['refer_code'] ) ) {
		wrc_set_cookie( 'refer_code', 0, time() - HOUR_IN_SECONDS );
		unset( $_COOKIE['refer_code'] );
	}
}
