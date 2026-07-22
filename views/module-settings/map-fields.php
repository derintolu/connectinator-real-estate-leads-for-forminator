<?php
/**
 * Per-form: map Forminator fields to Follow Up Boss fields.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$ffub_vars = array(
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
foreach ( $template_vars as $ffub_key => $ffub_val ) {
	$ffub_vars[ $ffub_key ] = $ffub_val;
}
?>

<div class="forminator-integration-popup__header">
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;"><?php esc_html_e( 'Assign fields', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></h3>
	<p id="forminator-integration-popup__description" class="sui-description"><?php esc_html_e( 'Match your form fields to Follow Up Boss contact fields.', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></p>
	<?php if ( ! empty( $ffub_vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $ffub_vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<table class="sui-table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Follow Up Boss Field', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></th>
				<th><?php esc_html_e( 'Forminator Field', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $ffub_vars['fields'] as $ffub_key => $ffub_field_title ) : ?>
			<?php
			$ffub_forminator_fields = ( 'email' === $ffub_key ) ? $ffub_vars['email_fields'] : $ffub_vars['module_fields'];
			$ffub_current_error     = ! empty( $ffub_vars[ $ffub_key . '_error' ] ) ? $ffub_vars[ $ffub_key . '_error' ] : '';
			$ffub_current_selected  = ! empty( $ffub_vars['fields_map'][ $ffub_key ] ) ? $ffub_vars['fields_map'][ $ffub_key ] : '';
			?>
			<tr>
				<td><?php echo esc_html( $ffub_field_title ); ?></td>
				<td>
					<div class="sui-form-field <?php echo esc_attr( ! empty( $ffub_current_error ) ? 'sui-form-field-error' : '' ); ?>">
						<select class="sui-select sui-select-sm" name="fields_map[<?php echo esc_attr( $ffub_key ); ?>]" data-placeholder="<?php esc_attr_e( 'Select a field', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?>">
							<option></option>
							<?php foreach ( $ffub_forminator_fields as $ffub_field_key => $ffub_field_label ) : ?>
								<option value="<?php echo esc_attr( $ffub_field_key ); ?>" <?php selected( $ffub_current_selected, $ffub_field_key ); ?>>
									<?php echo esc_html( $ffub_field_label . ' | ' . $ffub_field_key ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if ( ! empty( $ffub_current_error ) ) : ?>
							<span class="sui-error-message"><?php echo esc_html( $ffub_current_error ); ?></span>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="multi_id" value="<?php echo esc_attr( $ffub_vars['multi_id'] ); ?>">
</form>
