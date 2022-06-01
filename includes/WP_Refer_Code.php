<?php

/**
 * this class is responsible for assigning ref code to user and
 * get refer code related data
 */
class WP_Refer_Code {

	/**
	 * @var int
	 */
	private $user_id;
	/**
	 * @var string
	 */
	private $ref_code;


	/**
	 * WP_Refer_Code constructor.
	 *
	 * @param $user_id
	 *
	 * @throws RuntimeException
	 */
	public function __construct( $user_id ) {
		if ( ! get_user_by( 'id', $user_id ) ) {
			throw new RuntimeException( "User with id $user_id does'nt exists" );
		}

		$user_ref_code = get_user_meta( $user_id, 'wrc_ref_code', true );
		if ( empty( $user_ref_code ) ) {
			$user_ref_code = Shalior_Referral_Code_Generator::getInstance()->get_ref_code();
			update_user_meta( $user_id, 'wrc_ref_code', $user_ref_code );
		}
		$this->ref_code = $user_ref_code;
		$this->user_id  = $user_id;
	}

	/**
	 * @param $ref_code
	 *
	 * @return int | false
	 */
	public static function get_user_id_by_ref_code( $ref_code ) {
		/** @var WP_User $user */
		$user = get_users(
			array(
				'meta_key'   => 'wrc_ref_code',
				'meta_value' => $ref_code,
				'fields'     => array( 'ID' ),
			)
		);

		return ! empty( $user ) ? $user[0]->ID : false;
	}

	/**
	 * @return string
	 */
	public function get_ref_code() {
		return $this->ref_code;
	}

	/**
	 * @param string $ref_code
	 */
	public function set_ref_code( $ref_code ) {

		$this->ref_code = $ref_code;
		if ( ! metadata_exists( 'user', $this->user_id, 'wrc_ref_code' ) ) {
			update_user_meta( $this->user_id, 'wrc_ref_code', $ref_code );
		}

	}

	public function update_ref_code( $ref_code ) {
		if ( metadata_exists( 'user', $this->user_id, 'wrc_ref_code' ) ) {
			$this->ref_code = $ref_code;

			return update_user_meta( $this->user_id, 'wrc_ref_code', $ref_code );
		}

		return false;
	}

	/**
	 * return the id of user who referred this user, false if referred by no one
	 */
	public function get_referrer_id() {

		return get_user_meta( $this->user_id, 'wrc_referrer_id', true );
	}

	/**
	 * @return string
	 */
	public function get_ref_link() {
		global $wp_referral_code_options;
		$ref_link = add_query_arg( array( wrc_get_ref_code_query() => $this->ref_code ), $wp_referral_code_options['register_url'] );

		return esc_url( $ref_link );
	}

	/**
	 * returns array of user ids invited by this user
	 *
	 * @param null $user_id
	 *
	 * @return array
	 */
	public function get_invited_users_id( $user_id = null ) {
		if ( $user_id === null ) {
			$user_id = $this->user_id;
		}
		$invited = get_user_meta( $user_id, 'wrc_invited_users', true );

		return empty( $invited ) ? array() : $invited;
	}
}
