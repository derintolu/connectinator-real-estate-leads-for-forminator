<?php
/**
 * Follow Up Boss — Forminator integration (global connect screen).
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

final class Forminator_Followupboss extends Forminator_Integration {

	/**
	 * Instance (enables the inherited get_instance()).
	 *
	 * @var self|null
	 */
	protected static $instance = null;

	protected $_slug                  = 'followupboss';
	protected $_version               = FFUB_VERSION;
	protected $_min_forminator_version = '1.1';
	protected $_short_title           = 'FUB';
	protected $_title                 = 'Follow Up Boss';
	protected $_position              = 20;
	protected $_url                   = 'https://www.followupboss.com/';

	/**
	 * Encrypted at rest.
	 *
	 * @var array
	 */
	protected $_sensitive_keys = array( 'api_key' );

	public function __construct() {
		$this->_description    = esc_html__( 'Send Forminator leads to Follow Up Boss.', 'connectinator-real-estate-leads-for-forminator' );
		$this->is_multi_global = false;
	}

	/** Show the integration in Forminator → Integrations. */
	public function is_settings_available() {
		return true;
	}

	/** Allow more than one Follow Up Boss connection per form. */
	public function is_allow_multi_on_form() {
		return true;
	}

	/**
	 * Icons/images live in this plugin's own assets/ folder.
	 *
	 * The base class resolves assets to forminator/addons/pro/{slug}/assets/,
	 * which doesn't exist for an external plugin — overriding this points
	 * get_icon()/get_image()/etc. at our bundled logo so the UI isn't broken.
	 */
	public function assets_path(): string {
		return trailingslashit( FFUB_URL ) . 'assets/';
	}

	/** Single-step global connect wizard. */
	public function settings_wizards() {
		return array(
			array(
				'callback'     => array( $this, 'setup_api' ),
				'is_completed' => array( $this, 'is_authorized' ),
			),
		);
	}

	/**
	 * Connect screen: collect + validate the API key and default routing.
	 *
	 * @param array $submitted_data Submitted data.
	 * @param int   $form_id        Form id (0 on global settings).
	 * @return array
	 */
	public function setup_api( $submitted_data, $form_id = 0 ) {
		$settings_values = $this->get_settings_values();
		$template        = FFUB_DIR . 'views/settings/setup-api.php';
		$template_params = array(
			'api_key'       => '',
			'api_key_error' => '',
			'system'        => $settings_values['system'] ?? 'Forminator',
			'source'        => $settings_values['source'] ?? '',
			'event_type'    => $settings_values['event_type'] ?? 'Registration',
			'tags'          => $settings_values['tags'] ?? '',
			'event_types'   => self::event_types(),
			'error_message' => '',
		);
		$has_errors   = false;
		$show_success = false;
		$buttons      = array();
		$is_submit    = ! empty( $submitted_data );

		foreach ( array( 'api_key', 'system', 'source', 'event_type', 'tags' ) as $key ) {
			if ( isset( $submitted_data[ $key ] ) ) {
				$template_params[ $key ] = $submitted_data[ $key ];
			}
		}

		if ( $is_submit ) {
			try {
				$api_key = $this->get_real_value( $submitted_data['api_key'] ?? '', 'api_key' );
				if ( empty( $api_key ) ) {
					throw new Forminator_Integration_Exception( esc_html__( 'Please enter your Follow Up Boss API key.', 'connectinator-real-estate-leads-for-forminator' ) );
				}
				$this->validate_api( $api_key );

				if ( ! forminator_addon_is_active( $this->_slug ) ) {
					$activated = Forminator_Integration_Loader::get_instance()->activate_addon( $this->_slug );
					if ( ! $activated ) {
						throw new Forminator_Integration_Exception( esc_html( Forminator_Integration_Loader::get_instance()->get_last_error_message() ) );
					}
				}

				$this->save_settings_values(
					array(
						'api_key'    => $api_key,
						'system'     => sanitize_text_field( $submitted_data['system'] ?? 'Forminator' ),
						'source'     => sanitize_text_field( $submitted_data['source'] ?? '' ),
						'event_type' => in_array( $submitted_data['event_type'] ?? '', self::event_types(), true ) ? $submitted_data['event_type'] : 'Registration',
						'tags'       => sanitize_text_field( $submitted_data['tags'] ?? '' ),
					)
				);

				if ( empty( $form_id ) ) {
					$show_success = true;
				}
			} catch ( Forminator_Integration_Exception $e ) {
				$template_params['api_key_error'] = $e->getMessage();
				$template_params['error_message'] = $this->connection_failed();
				$has_errors                       = true;
			}
		}

		if ( $show_success ) {
			$html = $this->success_authorize();
		} else {
			if ( $this->is_connected() ) {
				$buttons['disconnect'] = array(
					'markup' => self::get_button_markup( esc_html__( 'Disconnect', 'connectinator-real-estate-leads-for-forminator' ), 'sui-button-ghost forminator-addon-disconnect' ),
				);
			}
			$buttons['submit'] = array(
				'markup' => '<div class="sui-actions-right">' .
					self::get_button_markup( esc_html__( 'Connect', 'connectinator-real-estate-leads-for-forminator' ), 'forminator-addon-connect' ) .
					'</div>',
			);
			$html = self::get_template( $template, $template_params );
		}

		return array(
			'html'       => $html,
			'buttons'    => $buttons,
			'redirect'   => false,
			'has_errors' => $has_errors,
		);
	}

	/** Connected when an API key is stored. */
	public function is_authorized() {
		$values = $this->get_settings_values();
		return ! empty( $values['api_key'] );
	}

	/**
	 * Validate the key with a read-only identity call.
	 *
	 * @throws Forminator_Integration_Exception When the key is invalid.
	 */
	public function validate_api( $api_key ) {
		$api      = new Forminator_Followupboss_Api( $api_key );
		$identity = $api->get_identity(); // throws on non-2xx
		if ( empty( $identity['account'] ) ) {
			throw new Forminator_Integration_Exception( esc_html__( 'Could not verify the Follow Up Boss account for this key.', 'connectinator-real-estate-leads-for-forminator' ) );
		}
	}

	/** API client using the decrypted stored key. */
	public function get_api() {
		$values = $this->get_settings_values( true );
		return new Forminator_Followupboss_Api( $values['api_key'] ?? '' );
	}

	public static function event_types() {
		return array( 'Registration', 'Inquiry', 'General Inquiry', 'Property Inquiry', 'Seller Inquiry', 'Property Search' );
	}

	/**
	 * Build a Follow Up Boss Events payload from mapped values.
	 *
	 * @param self   $addon     Integration instance.
	 * @param array  $person    firstName/lastName/email/phone.
	 * @param string $message   Human-readable field dump.
	 * @param string $form_name Fallback source label.
	 * @return array
	 */
	public static function build_event( $addon, array $person, $message, $form_name ) {
		$settings = $addon->get_settings_values();

		$out = array();
		if ( ! empty( $person['firstName'] ) ) {
			$out['firstName'] = $person['firstName'];
		}
		if ( ! empty( $person['lastName'] ) ) {
			$out['lastName'] = $person['lastName'];
		}
		if ( ! empty( $person['email'] ) ) {
			$out['emails'] = array( array( 'value' => $person['email'] ) );
		}
		if ( ! empty( $person['phone'] ) ) {
			$out['phones'] = array( array( 'value' => $person['phone'] ) );
		}
		$tags = array_values( array_filter( array_map( 'trim', explode( ',', (string) ( $settings['tags'] ?? '' ) ) ) ) );
		if ( $tags ) {
			$out['tags'] = $tags;
		}

		return array(
			'source'  => ! empty( $settings['source'] ) ? $settings['source'] : $form_name,
			'system'  => $settings['system'] ?? 'Forminator',
			'type'    => $settings['event_type'] ?? 'Registration',
			'message' => $message,
			'person'  => $out,
		);
	}

	/**
	 * Send a Forminator entry to Follow Up Boss with auto-detected field mapping.
	 * Used for programmatic entries (forminator_fub/push) that have no per-form
	 * connection configured. Requires only a connected global API key.
	 */
	public static function push_entry( $form_id, $entry_id ) {
		if ( ! class_exists( 'Forminator_API' ) ) {
			return;
		}
		$addon = self::get_instance();
		if ( ! $addon->is_authorized() ) {
			return;
		}

		$form  = Forminator_API::get_form( $form_id );
		$entry = Forminator_API::get_entry( $form_id, $entry_id );
		if ( is_wp_error( $form ) || is_wp_error( $entry ) || ! $entry ) {
			return;
		}

		$fields = method_exists( $form, 'get_fields' ) ? (array) $form->get_fields() : array();
		$email  = '';
		$phone  = '';
		$first  = '';
		$last   = '';
		$lines  = array();

		foreach ( $fields as $field ) {
			$slug = $field->slug ?? '';
			if ( ! $slug ) {
				continue;
			}
			$type  = $field->__get( 'type' );
			$label = $field->__get( 'field_label' );
			$raw   = method_exists( $entry, 'get_meta' ) ? $entry->get_meta( $slug ) : ( $entry->meta_data[ $slug ]['value'] ?? '' );

			if ( 'name' === $type && is_array( $raw ) ) {
				$first = $first ?: (string) ( $raw['first-name'] ?? '' );
				$last  = $last ?: (string) ( $raw['last-name'] ?? '' );
				$val   = trim( $first . ' ' . $last );
			} elseif ( is_array( $raw ) ) {
				$val = trim( implode( ' ', array_filter( array_map( 'strval', $raw ), static fn( $v ) => '' !== $v ) ) );
			} else {
				$val = trim( (string) $raw );
			}
			if ( '' === $val ) {
				continue;
			}

			if ( 'email' === $type && ! $email ) {
				$email = $val;
			} elseif ( 'phone' === $type && ! $phone ) {
				$phone = $val;
			} elseif ( 'name' !== $type ) {
				if ( ! $first && preg_match( '/first\s*name/i', (string) $label ) ) {
					$first = $val;
				} elseif ( ! $last && preg_match( '/last\s*name/i', (string) $label ) ) {
					$last = $val;
				}
			}
			$lines[] = ( $label ? $label : $slug ) . ': ' . $val;
		}

		if ( ! $email && ! $phone ) {
			return;
		}

		$form_name = ( isset( $form->name ) && $form->name ) ? (string) $form->name : ( 'Forminator Form #' . (int) $form_id );
		$event     = self::build_event(
			$addon,
			array( 'firstName' => $first, 'lastName' => $last, 'email' => $email, 'phone' => $phone ),
			implode( "\n", $lines ),
			$form_name
		);

		/**
		 * Filter the Follow Up Boss event payload before it is sent.
		 *
		 * @param array  $event    Events API payload (source/system/type/message/person).
		 * @param int    $form_id  Forminator form id the entry belongs to.
		 * @param int    $entry_id Forminator entry id.
		 * @param object $addon    The integration instance.
		 */
		$event = apply_filters( 'forminator_fub/event', $event, (int) $form_id, (int) $entry_id, $addon );

		try {
			$addon->get_api()->create_event( $event );
		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[forminator-fub] ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Guarded debug logging for a silent async push failure.
			}
		}
	}
}
