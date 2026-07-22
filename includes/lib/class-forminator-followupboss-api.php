<?php
/**
 * Follow Up Boss API client.
 *
 * Events API: https://docs.followupboss.com/reference/events-post
 * Auth: HTTP Basic, API key as the username with an empty password.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

class Forminator_Followupboss_Api {

	const BASE = 'https://api.followupboss.com/v1';

	/**
	 * API key.
	 *
	 * @var string
	 */
	protected $api_key;

	/**
	 * Diagnostics captured for the Forminator submissions log.
	 *
	 * @var mixed
	 */
	protected $last_data_sent     = null;
	protected $last_data_received = null;
	protected $last_url_request   = null;

	public function __construct( $api_key ) {
		$this->api_key = (string) $api_key;
	}

	/**
	 * Perform a request.
	 *
	 * @throws Forminator_Integration_Exception On transport or HTTP error.
	 * @return array|null
	 */
	protected function request( $method, $path, $body = null ) {
		$url                    = self::BASE . $path;
		$this->last_url_request = $url;

		$args = array(
			'method'  => $method,
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->api_key . ':' ),
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
			),
		);
		if ( null !== $body ) {
			$args['body']         = wp_json_encode( $body );
			$this->last_data_sent = $body;
		}

		$response = wp_remote_request( $url, $args );
		if ( is_wp_error( $response ) ) {
			throw new Forminator_Integration_Exception( esc_html( $response->get_error_message() ) );
		}

		$code                     = (int) wp_remote_retrieve_response_code( $response );
		$decoded                  = json_decode( wp_remote_retrieve_body( $response ), true );
		$this->last_data_received = $decoded;

		if ( $code < 200 || $code >= 300 ) {
			$message = is_array( $decoded ) && ! empty( $decoded['errorMessage'] )
				? $decoded['errorMessage']
				: sprintf( /* translators: %d: HTTP status code. */ esc_html__( 'Follow Up Boss returned HTTP %d.', 'real-estate-leads-for-forminator' ), $code );
			throw new Forminator_Integration_Exception( esc_html( $message ) );
		}

		return is_array( $decoded ) ? $decoded : array();
	}

	/** Read-only account check used to validate the API key. */
	public function get_identity() {
		return $this->request( 'GET', '/identity' );
	}

	/** Create an event (adds/updates a person and logs the lead). */
	public function create_event( array $event ) {
		return $this->request( 'POST', '/events', $event );
	}

	public function get_last_data_sent() {
		return $this->last_data_sent;
	}

	public function get_last_data_received() {
		return $this->last_data_received;
	}

	public function get_last_url_request() {
		return $this->last_url_request;
	}
}
