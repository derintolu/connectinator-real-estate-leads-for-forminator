<?php
/**
 * Per-form settings: name the connection and map form fields to Follow Up Boss.
 *
 * Class name is mandatory: Forminator derives it as
 * Forminator_<Ucfirst(slug)>_Form_Settings.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

class Forminator_Followupboss_Form_Settings extends Forminator_Integration_Form_Settings {

	/** Destination fields offered for mapping. */
	private function destination_fields() {
		return array(
			'email'      => esc_html__( 'Email Address', 'connectinator-real-estate-leads-for-forminator' ),
			'first_name' => esc_html__( 'First Name', 'connectinator-real-estate-leads-for-forminator' ),
			'last_name'  => esc_html__( 'Last Name', 'connectinator-real-estate-leads-for-forminator' ),
			'phone'      => esc_html__( 'Phone', 'connectinator-real-estate-leads-for-forminator' ),
		);
	}

	public function module_settings_wizards() {
		return array(
			array(
				'callback'     => array( $this, 'pick_name' ),
				'is_completed' => array( $this, 'pick_name_is_completed' ),
			),
			array(
				'callback'     => array( $this, 'map_fields' ),
				'is_completed' => array( $this, 'map_fields_is_completed' ),
			),
		);
	}

	/** Step 1 — friendly connection name. */
	public function pick_name( $submitted_data ) {
		$template = FFUB_DIR . 'views/module-settings/pick-name.php';

		$multi_id = $this->generate_multi_id();
		if ( isset( $submitted_data['multi_id'] ) ) {
			$multi_id = $submitted_data['multi_id'];
		}

		$template_params = array(
			'name'       => $this->get_multi_id_settings( $multi_id, 'name' ),
			'name_error' => '',
			'multi_id'   => $multi_id,
		);
		unset( $submitted_data['multi_id'] );

		$is_submit  = ! empty( $submitted_data );
		$has_errors = false;

		if ( $is_submit ) {
			$name                    = isset( $submitted_data['name'] ) ? sanitize_text_field( $submitted_data['name'] ) : '';
			$template_params['name'] = $name;
			try {
				if ( empty( $name ) ) {
					throw new Forminator_Integration_Exception( esc_html__( 'Please enter a name for this connection.', 'connectinator-real-estate-leads-for-forminator' ) );
				}
				$this->save_multi_id_setting_values(
					$multi_id,
					array( 'name' => $name, 'time_added' => $this->get_multi_id_settings( $multi_id, 'time_added', time() ) )
				);
			} catch ( Forminator_Integration_Exception $e ) {
				$template_params['name_error'] = $e->getMessage();
				$has_errors                    = true;
			}
		}

		$buttons                   = array();
		$buttons['next']['markup'] = '<div class="sui-actions-right">' .
			Forminator_Integration::get_button_markup( esc_html__( 'Next', 'connectinator-real-estate-leads-for-forminator' ), 'forminator-addon-next' ) .
			'</div>';

		return array(
			'html'       => Forminator_Integration::get_template( $template, $template_params ),
			'buttons'    => $buttons,
			'redirect'   => false,
			'has_errors' => $has_errors,
		);
	}

	public function pick_name_is_completed( $submitted_data ) {
		$multi_id = $submitted_data['multi_id'] ?? '';
		if ( empty( $multi_id ) ) {
			return false;
		}
		return ! empty( $this->get_multi_id_settings( $multi_id, 'name' ) );
	}

	/** Step 2 — map fields. */
	public function map_fields( $submitted_data ) {
		$template = FFUB_DIR . 'views/module-settings/map-fields.php';

		if ( ! isset( $submitted_data['multi_id'] ) ) {
			return $this->get_force_closed_wizard();
		}
		$multi_id = $submitted_data['multi_id'];
		unset( $submitted_data['multi_id'] );

		$email_fields                 = $this->get_fields_for_type( 'email' );
		$form_fields                  = $this->get_fields_for_type();
		$forminator_field_element_ids = wp_list_pluck( $form_fields, 'element_id' );
		$module_fields                = wp_list_pluck( $this->form_fields, 'field_label', 'element_id' );
		$fields                       = $this->destination_fields();

		$template_params = array(
			'fields_map'    => $this->get_multi_id_settings( $multi_id, 'fields_map', array() ),
			'multi_id'      => $multi_id,
			'error_message' => '',
			'fields'        => $fields,
			'module_fields' => $module_fields,
			'email_fields'  => wp_list_pluck( $email_fields, 'field_label', 'element_id' ),
		);

		$is_submit  = ! empty( $submitted_data );
		$has_errors = false;

		if ( $is_submit ) {
			$fields_map                    = $submitted_data['fields_map'] ?? array();
			$template_params['fields_map'] = $fields_map;
			try {
				$input_exceptions = new Forminator_Integration_Settings_Exception();
				if ( empty( $fields_map['email'] ) && empty( $fields_map['phone'] ) ) {
					$input_exceptions->add_input_exception( esc_html__( 'Map at least an Email or Phone field.', 'connectinator-real-estate-leads-for-forminator' ), 'email_error' );
				}
				foreach ( $fields as $key => $title ) {
					if ( ! empty( $fields_map[ $key ] ) && ! in_array( $fields_map[ $key ], $forminator_field_element_ids, true ) ) {
						$input_exceptions->add_input_exception(
							sprintf( /* translators: %s: field title. */ esc_html__( 'Please assign a valid field for %s', 'connectinator-real-estate-leads-for-forminator' ), esc_html( $title ) ),
							$key . '_error'
						);
					}
				}
				if ( $input_exceptions->input_exceptions_is_available() ) {
					throw $input_exceptions;
				}
				$this->save_multi_id_setting_values( $multi_id, array( 'fields_map' => $fields_map ) );
			} catch ( Forminator_Integration_Settings_Exception $e ) {
				$template_params = array_merge( $template_params, $e->get_input_exceptions() );
				$has_errors      = true;
			} catch ( Forminator_Integration_Exception $e ) {
				$template_params['error_message'] = $e->getMessage();
				$has_errors                       = true;
			}
		}

		$buttons = array(
			'disconnect' => array(
				'markup' => Forminator_Integration::get_button_markup( esc_html__( 'Deactivate', 'connectinator-real-estate-leads-for-forminator' ), 'sui-button-ghost forminator-addon-form-disconnect' ),
			),
			'submit'     => array(
				'markup' => '<div class="sui-actions-right">' .
					Forminator_Integration::get_button_markup( esc_html__( 'Save', 'connectinator-real-estate-leads-for-forminator' ), 'forminator-addon-finish' ) .
					'</div>',
			),
		);

		return array(
			'html'       => Forminator_Integration::get_template( $template, $template_params ),
			'buttons'    => $buttons,
			'redirect'   => false,
			'has_errors' => $has_errors,
		);
	}

	public function map_fields_is_completed( $submitted_data ) {
		$multi_id = $submitted_data['multi_id'] ?? '';
		if ( empty( $multi_id ) ) {
			return false;
		}
		$map = $this->get_multi_id_settings( $multi_id, 'fields_map', array() );
		return ! empty( $map['email'] ) || ! empty( $map['phone'] );
	}
}
