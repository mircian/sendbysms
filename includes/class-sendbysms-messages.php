<?php


class SendBySMS_Messages {

	/**
	 * The single instance of the class.
	 *
	 * @var SendBySMS_Messages
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * The enabled messages.
	 *
	 * @var array
	 */
	public $enabled_messages = [];

	/**
	 * The messages content.
	 *
	 * @var array
	 */
	public $messages_content = [];

	public function __construct() {
		$this->load_messages();
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

	public function load_messages() {
		$enabled_messages_option = get_option( 'sendbysms_enabled_messages' );

		$this->enabled_messages = [];
		if ( ! empty( $enabled_messages_option ) && is_array( $enabled_messages_option ) ) {
			foreach ( $enabled_messages_option as $message_id => $enabled ) {
				if ( 'yes' === $enabled ) {
					$this->enabled_messages[] = $message_id;
				}
			}
		}

		if ( ! empty( $this->enabled_messages ) ) {
			$messages_content       = get_option( 'sendbysms_messages' );
			$this->messages_content = [];
			foreach ( $this->enabled_messages as $message_key ) {
				if ( isset( $messages_content[ $message_key ] ) ) {
					$this->messages_content[ $message_key ] = $messages_content[ $message_key ];
				}
			}
		}
	}


	/**
	 * Checks if the specified sms message is enabled.
	 *
	 * @param string $message The message key.
	 *
	 * return boolean
	 */
	public function is_message_enabled( $message ) {

		$enabled_messages = apply_filters( 'sendbysms_enabled_messages', $this->enabled_messages );

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

		if ( $this->is_message_enabled( $key ) ) {
			$message = $this->replace_tags( $this->messages_content[ $key ], $order );
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
