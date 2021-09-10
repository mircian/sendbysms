<?php


class SendBySMS_Messages {

	/**
	 * The single instance of the class.
	 *
	 * @var SendBySMS_Messages
	 * @since 1.0
	 */
	protected static $_instance = null;

	public $messages = [
		'payment_complete',
	];

	public function __construct() {

	}

	/**
	 * Main SendBySMS Messages Instance.
	 *
	 * Ensures only one instance of the instance is loaded.
	 *
	 * @return SendBySMS_Messages - Main instance.
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * Checks if the specified sms message is enabled.
	 *
	 * @param string $message The message key.
	 *
	 * return boolean
	 */
	public function is_message_enabled( $message ) {

		$enabled_messages = apply_filters( 'sendbysms_enabled_messages', $this->messages );

		return in_array( $message, $enabled_messages, true );

	}

	/**
	 * Get the message content based on the key.
	 *
	 * @param string       $key The message key to select the message from the list.
	 * @param WC_Order|int $order Order object or order id.
	 */
	public function get_message_content( $key, $order ) {

		// Attempt to retrieve order if id is passed.
		if ( is_int( $order ) ) {
			$order = wc_get_order( $order );
		}

		$message_value = get_option( 'sendbysms_message_' . $key );

		if ( ! empty ( $message_value ) ) {
			$message = $this->replace_tags( $message_value, $order );
		}

		if ( empty( $message ) ) {
			return false;
		}

		return $message;

	}

	/**
	 * Replace message shorttags.
	 *
	 * @param string   $message The message string before processing.
	 * @param WC_Order $order The order object.
	 *
	 * @return string
	 */
	public function replace_tags( $message, $order ) {

		$tags = $this->get_tags( $order );

		return str_replace( $tags['tags'], $tags['values'], $message );

	}

	/**
	 * @param WC_Order $order The order to build the replacement for.
	 *
	 * @return array
	 */
	public function get_tags( $order ) {
		// Allow plugin developers to add custom tags.
		$relation = apply_filters( 'sendbysms_tags', [
			'[order_id]'        => $order->get_id(),
			'[order_total]'     => $order->get_total(),
			'[shipping_method]' => $order->get_shipping_method(),
			'[shop_name]'       => get_bloginfo( 'name' ),
		] );

		// Split into 2 arrays.
		return [
			'tags'   => array_keys( $relation ),
			'values' => array_values( $relation ),
		];
	}

}
