<?php
/**
 * Make API requests to the SendBySMS API to send messages.
 */

/**
 * Class used to handle sending the SMS to the SendBySMS API.
 */
class SendBySMS_Sender {

	/**
	 * The single instance of the class.
	 *
	 * @var SendBySMS_Sender
	 * @since 1.0
	 */
	protected static $_instance = null;

	protected $api_url = 'https://dashboard.sendbysms.app/api/v3/';
	protected $api_token;
	protected $sender_id;

	/**
	 * Main SendBySMS Sender Instance.
	 *
	 * Ensures only one instance of the instance is loaded.
	 *
	 * @return SendBySMS_Sender - Main instance.
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
	 * Constructor - does nothing for now.
	 */
	public function __construct() {
	}

	/**
	 * Get the base API url.
	 *
	 * @return string
	 */
	public function get_api_url() {
		return apply_filters( 'sendbysms_api_url', $this->api_url );
	}

	/**
	 * Get the SendBySMS API token as stored in the options or from constant.
	 *
	 * @return false|mixed|void
	 */
	public function get_api_token() {
		// Allow the use of a constant if you don't want to store the token in the db.
		if ( defined( 'SEND_BY_SMS_API_TOKEN' ) ) {
			return SEND_BY_SMS_API_TOKEN;
		}
		if ( ! isset( $this->api_token ) ) {
			$this->api_token = get_option( 'wc_settings_tab_sendbysms_token' );
		}

		return $this->api_token;
	}

	/**
	 * Sender id.
	 *
	 * @return mixed|void
	 */
	public function get_sender_id() {

		if ( ! isset( $this->sender_id ) ) {
			$this->sender_id = apply_filters( 'sendbysms_sender_id', 'SendBySMS' );
		}

		return $this->sender_id;
	}

	/**
	 * Send a sms.
	 *
	 * @param string $recipient The phone number you're sending the sms to.
	 * @param string $message The SMS message content.
	 */
	public function send_sms( $recipient, $message ) {

		$url     = trailingslashit( $this->get_api_url() ) . 'sms/send';
		$headers = [
			'Authorization' => 'Bearer ' . $this->get_api_token(),
			'Accept' => 'application/json',
		];
		$args    = [
			'headers'  => $headers,
			'blocking' => apply_filters( 'sendbysms_debug', true ),
			'body'     => json_encode([
				'recipient' => $recipient,
				'sender_id' => $this->get_sender_id(),
				'message'   => $message,
			]),
		];

		$sent = wp_remote_post( $url, $args );

	}

}
