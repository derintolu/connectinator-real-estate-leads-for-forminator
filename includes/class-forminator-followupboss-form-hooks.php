<?php
/**
 * Form hooks: send the entry to Follow Up Boss on submission.
 *
 * Class name is mandatory: Forminator_<Ucfirst(slug)>_Form_Hooks.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

class Forminator_Followupboss_Form_Hooks extends Forminator_Integration_Form_Hooks {

	/**
	 * Runs per submission; one outbound call per completed connection.
	 *
	 * @param array $submitted_data       Submitted values keyed by element_id.
	 * @param array $current_entry_fields Existing entry fields.
	 * @return array Rows of ['name' => 'status-<multi_id>', 'value' => result].
	 */
	protected function custom_entry_fields( $submitted_data, $current_entry_fields ): array {
		$connections = $this->settings_instance->get_settings_values();
		$data        = array();

		foreach ( $connections as $multi_id => $connection ) {
			if ( ! $this->settings_instance->is_multi_id_completed( $multi_id ) ) {
				continue;
			}
			$data[] = array(
				'name'  => 'status-' . $multi_id,
				'value' => $this->send( $multi_id, $submitted_data, $connection ),
			);
		}

		return $data;
	}

	/**
	 * Map a single connection and send it.
	 *
	 * @return array Result row for the Forminator submissions log.
	 */
	private function send( $multi_id, $submitted_data, $connection ) {
		$name = $connection['name'] ?? $multi_id;

		try {
			$map = $connection['fields_map'] ?? array();

			$get = static function ( $key ) use ( $map, $submitted_data ) {
				$element_id = $map[ $key ] ?? '';
				return ( $element_id && isset( $submitted_data[ $element_id ] ) ) ? trim( (string) $submitted_data[ $element_id ] ) : '';
			};

			$person = array(
				'firstName' => $get( 'first_name' ),
				'lastName'  => $get( 'last_name' ),
				'email'     => $get( 'email' ),
				'phone'     => $get( 'phone' ),
			);
			if ( '' === $person['email'] && '' === $person['phone'] ) {
				throw new Forminator_Integration_Exception( esc_html__( 'No email or phone in the submission.', 'real-estate-leads-for-forminator' ) );
			}

			// Human-readable dump of every submitted field for the event message.
			$lines = array();
			foreach ( $this->settings_instance->get_form_fields() as $field ) {
				$eid = $field['element_id'] ?? '';
				if ( $eid && isset( $submitted_data[ $eid ] ) && '' !== $submitted_data[ $eid ] ) {
					$label   = $field['field_label'] ?? $eid;
					$value   = is_array( $submitted_data[ $eid ] ) ? implode( ' ', $submitted_data[ $eid ] ) : $submitted_data[ $eid ];
					$lines[] = $label . ': ' . $value;
				}
			}

			$form   = Forminator_API::get_form( $this->module_id );
			$fname  = ( ! is_wp_error( $form ) && isset( $form->name ) && $form->name ) ? (string) $form->name : ( 'Forminator Form #' . (int) $this->module_id );
			$event  = Forminator_Followupboss::build_event( $this->addon, $person, implode( "\n", $lines ), $fname );
			/** @see Forminator_Followupboss::push_entry() for the filter contract. */
			$event  = apply_filters( 'forminator_fub/event', $event, (int) $this->module_id, 0, $this->addon );
			$api    = $this->addon->get_api();
			$api->create_event( $event );

			return array(
				'is_sent'         => true,
				'connection_name' => $name,
				'description'     => esc_html__( 'Sent to Follow Up Boss.', 'real-estate-leads-for-forminator' ),
				'data_sent'       => $api->get_last_data_sent(),
				'data_received'   => $api->get_last_data_received(),
				'url_request'     => $api->get_last_url_request(),
			);
		} catch ( Exception $e ) {
			return array(
				'is_sent'         => false,
				'connection_name' => $name,
				'description'     => $e->getMessage(),
			);
		}
	}
}
