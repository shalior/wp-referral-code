<?php
/**
 * Handles Creating ref codes
 * User: user
 * Date: 01-Feb-20
 * Time: 2:35 PM
 * @property  instance
 */

class Shalior_Referral_Code_Generator {
	private static $instance = null;

	public static function getInstance() {
		if ( self::$instance == null ) {
			self::$instance = new Shalior_Referral_Code_Generator();
		}

		return self::$instance;
	}

	/**
	 * generates a duplication safe  ref code
	 *
	 * @param null|int $length
	 *
	 * @return string
	 */
	public function
	get_ref_code(
		$length = null
	) {

		global $wp_referral_code_options;

		$code_length = empty( $length ) ? $wp_referral_code_options['code_length'] : $length;
		$ref_code    = $this->generateRefCode( $code_length );
		if ( $this->isUnique( $ref_code ) ) {
			return $ref_code;
		}

		$validated = false;
		do {
			$ref_code  = $this->generateRefCode( $code_length );
			$validated = $this->isUnique( $ref_code );
			if ( $validated ) {
				return $ref_code;
			}
		} while ( ! $validated );


		return $ref_code;
	}

	/**
	 * @param $length
	 *
	 * @return bool|string
	 */
	private function generateRefCode( $length ) {
		// generate crypto secure byte string
		$bytes = random_bytes( 8 );

		// convert to alphanumeric (also with =, + and /) string
		$encoded = base64_encode( $bytes );

		// remove the chars we don't want
		$stripped = substr( strtolower( str_replace( [ '=', '+', '/' ], '', $encoded ) ), 0, $length );

		// format the final referral code
		return $stripped;
	}

	private function isUnique( $ref_code ) {
		$user = get_users( [
			'meta_key'     => 'wrc_ref_code',
			'meta_value'   => $ref_code,
			'meta_compare' => '=',
			/*			'meta_type'    => 'BINARY',*/
			'fields'       => 'ids'
		] );
		if ( count( $user ) > 0 ) {
			return false;
		}

		return true;
	}

}




