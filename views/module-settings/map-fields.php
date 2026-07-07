<?php
/**
 * Per-form: map Forminator fields to Follow Up Boss fields.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$vars = array(
	'error_message' => '',
	'multi_id'      => '',
	'fields_map'    => array(),
	'fields'        => array(),
	'module_fields' => array(),
	'email_fields'  => array(),
);
/**
 * Template variables.
 *
 * @var array $template_vars
 */
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}
?>

<div class="forminator-integration-popup__header">
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;"><?php esc_html_e( 'Assign fields', 'lead-sync-for-follow-up-boss-forminator' ); ?></h3>
	<p id="forminator-integration-popup__description" class="sui-description"><?php esc_html_e( 'Match your form fields to Follow Up Boss contact fields.', 'lead-sync-for-follow-up-boss-forminator' ); ?></p>
	<?php if ( ! empty( $vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<table class="sui-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Follow Up Boss Field', 'lead-sync-for-follow-up-boss-forminator' ); ?></th>
				<th><?php esc_html_e( 'Forminator Field', 'lead-sync-for-follow-up-boss-forminator' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $vars['fields'] as $key => $field_title ) : ?>
			<?php
			$forminator_fields = ( 'email' === $key ) ? $vars['email_fields'] : $vars['module_fields'];
			$current_error     = ! empty( $vars[ $key . '_error' ] ) ? $vars[ $key . '_error' ] : '';
			$current_selected  = ! empty( $vars['fields_map'][ $key ] ) ? $vars['fields_map'][ $key ] : '';
			?>
			<tr>
				<td><?php echo esc_html( $field_title ); ?></td>
				<td>
					<div class="sui-form-field <?php echo esc_attr( ! empty( $current_error ) ? 'sui-form-field-error' : '' ); ?>">
						<select class="sui-select sui-select-sm" name="fields_map[<?php echo esc_attr( $key ); ?>]" data-placeholder="<?php esc_attr_e( 'Select a field', 'lead-sync-for-follow-up-boss-forminator' ); ?>">
							<option></option>
							<?php foreach ( $forminator_fields as $field_key => $field_label ) : ?>
								<option value="<?php echo esc_attr( $field_key ); ?>" <?php selected( $current_selected, $field_key ); ?>>
									<?php echo esc_html( $field_label . ' | ' . $field_key ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if ( ! empty( $current_error ) ) : ?>
							<span class="sui-error-message"><?php echo esc_html( $current_error ); ?></span>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="multi_id" value="<?php echo esc_attr( $vars['multi_id'] ); ?>">
</form>
