<?php

/**
 * Handles Creating ref codes
 */
class Shalior_Referral_Code_Generator {
	private static $instance = null;

	/**
	 * Get an instance of class
	 *
	 * @return Shalior_Referral_Code_Generator|null
	 * @deprecated Use get_instance instead
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new Shalior_Referral_Code_Generator();
		}

		return self::$instance;
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Shalior_Referral_Code_Generator();
		}

		return self::$instance;
	}

	/**
	 * Generates a duplication safe  ref code
	 *
	 * @param null|int $length
	 *
	 * @return string
	 */
	public function get_ref_code( $length = null ) {

		global $wp_referral_code_options;

		$code_length = empty( $length ) ? ( isset( $wp_referral_code_options['code_length'] ) ? $wp_referral_code_options['code_length'] : 6 ) : $length;
		$ref_code    = $this->generate_ref_code( $code_length );
		if ( $this->is_unique( $ref_code ) ) {
			return $ref_code;
		}

		$validated = false;
		do {
			$ref_code  = $this->generate_ref_code( $code_length );
			$validated = $this->is_unique( $ref_code );
			if ( $validated ) {
				return $ref_code;
			}
		} while ( ! $validated );

		return $ref_code;
	}

	/**
	 * Generate a random string for refer codes.
	 *
	 * @param int $length
	 *
	 * @return bool|string
	 */
	private function generate_ref_code( $length ) {
		// generate crypto secure byte string.
		try {
			$bytes = random_bytes( 8 );
		} catch ( Exception $exception ) {
			$bytes = substr( md5( wp_rand() ), 0, $length );

		}

		// convert to alphanumeric (also with =, + and /) string.
		$encoded = base64_encode( $bytes );

		// remove the chars we don't want.
		$stripped = substr( strtolower( str_replace( array( '=', '+', '/' ), '', $encoded ) ), 0, $length );

		// format the final referral code.
		return $stripped;
	}

	private function is_unique( $ref_code ) {
		$user = get_users(
			array(
				'meta_key'     => 'wrc_ref_code',
				'meta_value'   => $ref_code,
				'meta_compare' => '=',
				'fields'       => 'ids',
			)
		);
		if ( count( $user ) > 0 ) {
			return false;
		}

		return true;
	}
}
