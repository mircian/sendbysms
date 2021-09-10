<?php
/**
 * This file holds the WooCommerce order-related logic.
 */

/**
 * Class with WooCommerce orders sending logic.
 */
class SendBySMS_Orders {

	public function __construct() {
		$this->hooks();
	}

	private function hooks() {
		add_action( 'woocommerce_payment_complete', [ $this, 'send_payment_complete_message' ] );
		add_action( 'woocommerce_order_status_changed', [ $this, 'maybe_send_payment_complete_message' ], 15, 4 );
	}

	/**
	 * For gateways with manual confirmation we can't use "woocommerce_payment_complete".
	 *
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 * @param $order
	 */
	public function maybe_send_payment_complete_message( $order_id, $old_status, $new_status, $order ) {

		$is_bacs_check        = in_array( $order->get_payment_method(), [ 'bacs', 'cheque' ] );
		$is_bacs_check_status = in_array( $new_status, [ 'processing', 'completed' ], true );

		// TODO: Add meta to order to make sure we don't send the same SMS 2 times.
		if ( $is_bacs_check && $is_bacs_check_status ) {
			$this->send_payment_complete_message( $order_id );
		}
		// 2. For Cash on delivery payments
		if ( 'cod' === $order->get_payment_method() && 'completed' === $new_status ) {
			$this->send_payment_complete_message( $order_id );
		}
	}

	/**
	 * Send a sms message when the order is paid.
	 *
	 * @param int $order_id The order id.
	 */
	public function send_payment_complete_message( $order_id ) {

		if ( ! SendBySMS_Messages::instance()->is_message_enabled( 'payment_complete' ) ) {
			return;
		}

		$order       = wc_get_order( $order_id );
		$order_phone = $order->get_billing_phone();

		if ( ! empty( $order_phone ) ) {
			$message_content = SendBySMS_Messages::instance()->get_message_content( 'payment_complete', $order );
			SendBySMS_Sender::instance()->send_sms( $order_phone, $message_content );
		}

	}

}

new SendBySMS_Orders();
