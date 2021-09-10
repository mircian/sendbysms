<?php
/**
 * Plugin Name: WooCommerce SendBySMS integration
 * Plugin URI: https://sendbysms.app/
 * Description: Seamlessly integrate sending SMS messages to your WooCommerce customers using the SendBySMS platform.
 * Version: 1.0.0
 * Author: Mircea Sandu
 * Author URI: https://mircian.com
 * Text Domain: sendbysms
 * Domain Path: /languages/
 * Requires at least: 5.6
 * Requires PHP: 7.0
 *
 * @package SendBySMS
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SEND_BY_SMS_PLUGIN_FILE' ) ) {
	define( 'SEND_BY_SMS_PLUGIN_FILE', __FILE__ );
}

class SendBySMS {

	public function __construct() {
		$this->includes();
	}

	public function includes() {
		$plugin_path = plugin_dir_path( SEND_BY_SMS_PLUGIN_FILE );

		require_once $plugin_path . '/includes/class-sendbysms-settings.php';
		require_once $plugin_path . '/includes/class-sendbysms-sender.php';
		require_once $plugin_path . '/includes/class-sendbysms-orders.php';
	}

}

new SendBySMS();
