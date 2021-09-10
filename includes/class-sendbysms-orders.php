<?php
/**
 * This file holds the WooCommerce order-related logic.
 */

/**
 * Class with WooCommerce orders sending logic.
 */
class SendBySMS_Orders {

	public $messages = [
		'payment_complete',
	];

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_payment_complete', [ $this, 'send_payment_complete_message' ] );
	}

	/**
	 * Send a sms message when the order is paid.
	 *
	 * @param int $order_id The order id.
	 */
	public function send_payment_complete_message( $order_id ) {

		if ( ! $this->should_send_sms( 'payment_complete' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$order_phone = $order->get_billing_phone();

		if ( ! empty( $order_phone ) ) {
			SendBySMS_Sender::instance()->send_sms( $order_phone, 'Your order was received' );
		}

	}

	/**
	 * Checks if the specified sms message is enabled.
	 *
	 * @param $sms_name
	 *
	 * return boolean
	 */
	private function should_send_sms( $sms_name ) {

		$enabled_messages = apply_filters( 'sendbysms_enabled_messages', $this->messages );

		return in_array( $sms_name, $enabled_messages, true );

	}

}

new SendBySMS_Orders();
