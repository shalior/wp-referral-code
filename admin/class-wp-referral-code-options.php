<?php

/**
 * handles options of the plugin
 *
 * @access public
 * @package    WP_Referral_Code
 * @subpackage WP_Referral_Code/admin
 * @author     Shalior <contact@shalior.ir>
 */
final class WP_Referral_Code_Settings {

	private static $instance;
	public $page_slug = 'wp-referral-code';
	private $options;

	/**
	 * Sets up needed actions/filters for the admin options to initialize.
	 *
	 * @return void
	 * @since  2.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			return;
		}

		global $wp_referral_code_options;
		$this->load_settings();
		$this->options = $wp_referral_code_options;
	}

	/**
	 * Returns the instance.
	 *
	 * @return object
	 * @since  2.0.0
	 * @access public
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load_settings() {
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// get options
	}

	/**
	 * @param $args
	 */
	public function field_code_length( $args ) {
		$option = $this->options;
		// include html
		include_once WP_REFERRAL_CODE_PATH . 'admin/partials/options/field_code_length.php';
	}

	public function field_register_url( $args ) {
		$option = $this->options;
		// include html
		include_once WP_REFERRAL_CODE_PATH . 'admin/partials/options/filed_register_url.php';
	}

	public function field_expiration_time( $args ) {
		$option = $this->options;
		// include html
		include_once WP_REFERRAL_CODE_PATH . 'admin/partials/options/field_expiration_time.php';
	}

	public function section_1( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Wp Referral code.', 'wp-referral-code' ); ?></p>
		<?php
	}

	public function settings_init() {
		// register a new setting
		$args = array(
			'description'       => '',
			'sanitize_callback' => array( $this, 'sanitize_callback' ),
			'show_in_rest'      => false,
		);
		register_setting( $this->page_slug, 'wp_referral_code_options', $args );
		// register a new section
		add_settings_section(
			'wp_referral_code_section_1',
			__( 'Settings', 'wp-referral-code' ),
			array( $this, 'section_1' ),
			$this->page_slug
		);

		// code length field
		add_settings_field(
			'wp_referral_code_code_length', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Refer code length: 5-8 is recommended', 'wp-referral-code' ),
			array( $this, 'field_code_length' ),
			$this->page_slug,
			'wp_referral_code_section_1',
			array(
				'label_for' => 'code_length',
				'class'     => 'wrc_row',
			)
		);

		// register url length
		add_settings_field(
			'wp_referral_code_register_url', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Register url', 'wp-referral-code' ),
			array( $this, 'field_register_url' ),
			$this->page_slug,
			'wp_referral_code_section_1',
			array(
				'label_for' => 'register_url',
				'class'     => 'wrc_row',
			)
		);

		add_settings_field(
			'wp_referral_code_expiration_time', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Expiration time(hours)', 'wp-referral-code' ),
			array( $this, 'field_expiration_time' ),
			$this->page_slug,
			'wp_referral_code_section_1',
			array(
				'label_for' => 'expiration_time',
				'class'     => 'wrc_row',
			)
		);
	}

	public function sanitize_callback( $data ) {

		$have_err = false;

		// check if the data has changed
		if ( $this->options == $data ) {
			return $data;
		}
		// validate code_length
		if ( ! ( $data['code_length'] >= 3 && $data['code_length'] <= 10 && is_numeric( $data['code_length'] ) ) ) {
			add_settings_error( $this->page_slug, $this->page_slug, __( 'Chose a number between 3 & 10', 'wp-referral-code' ) );
			$have_err = true;
		}
		// validate register_url
		if ( wrc_is_valid_url( $data['register_url'] ) === false ) {
			add_settings_error( $this->page_slug, $this->page_slug, __( 'Register Url is not valid', 'wp-referral-code' ) );
			$have_err = true;
		}

		if ( empty( $data['expiration_time'] ) || ! is_numeric( $data['expiration_time'] ) ) {
			add_settings_error( $this->page_slug, $this->page_slug, __( 'Expiration time is not valid', 'wp-referral-code' ) );
			$have_err = true;
		}

		if ( ! $have_err ) {
			// reset ref codes if no err found and code_length value has changed
			if ( $this->options['code_length'] != $data['code_length'] ) {
				wrc_set_ref_code_all_users( true, $data['code_length'] );
			}

			// sanitize
			$data['code_length']     = sanitize_text_field( $data['code_length'] );
			$data['register_url']    = esc_url_raw( $data['register_url'] );
			$data['expiration_time'] = sanitize_text_field( $data['expiration_time'] );

			return $data;
		}

		return $this->options;
	}

	// menu methods
	public function add_options_page() {
		add_options_page(
			'WP Referral Code Options',
			'WP Referral Code',
			'manage_options',
			$this->page_slug,
			array( $this, 'get_options_page_html' )
		);
	}

	public function get_options_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once WP_REFERRAL_CODE_PATH . 'admin/partials/options/wp-referral-code-admin-setting-view.php';
	}

}

WP_Referral_Code_Settings::get_instance();
