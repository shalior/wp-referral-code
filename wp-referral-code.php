<?php
/**
 * WordPress referral code
 *
 * @link              http://shalior.ir
 * @since             1.1.1
 * @package           WP_Referral_Code
 *
 * @wordpress-plugin
 * Plugin Name:       WP Referral Code
 * Plugin URI:        http://shalior.ir/wp-referral-code
 * Description:       This plugin brings referral marketing to your WordPress website. It's dead simple, fast, customizable, and it's all free!
 * Version:           1.4.11
 * Author:            Shalior <contact@shalior.ir>
 * Author URI:        http://shalior.ir/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-referral-code
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// holds the plugin path.
define( 'WP_REFERRAL_CODE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_REFERRAL_CODE_URI', plugin_dir_url( __FILE__ ) );
define( 'WP_REFERRAL_CODE_VERSION', '1.4.11' );

/**
 * The code that runs during plugin activation.
 */
function activate_wp_referral_code() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-referral-code-activator.php';
	WP_Referral_Code_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_referral_code() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-referral-code-deactivator.php';
	WP_Referral_Code_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_referral_code' );
register_deactivation_hook( __FILE__, 'deactivate_wp_referral_code' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-referral-code.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_wp_referral_code() {

	WP_Referral_Code::get_instance();
}

// gets necessary options.
$wp_referral_code_options = get_option(
	'wp_referral_code_options',
	array(
		'code_length'                => 6,
		'register_url'               => wp_registration_url(),
		'expiration_time'            => 10,
		'show_referral_info_columns' => '1',
	)
);

// runs the plugin.
run_wp_referral_code();

do_action( 'wp_referral_code_loaded' );
