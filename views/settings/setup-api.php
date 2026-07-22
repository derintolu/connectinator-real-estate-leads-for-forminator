<?php
/**
 * Follow Up Boss connect screen (global settings).
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$ffub_vars = array(
	'api_key'       => '',
	'api_key_error' => '',
	'system'        => 'Forminator',
	'source'        => '',
	'event_type'    => 'Registration',
	'tags'          => '',
	'event_types'   => array( 'Registration' ),
	'error_message' => '',
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
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;">
		<?php esc_html_e( 'Connect Follow Up Boss', 'connectinator-real-estate-leads-for-forminator' ); ?>
	</h3>
	<p id="forminator-integration-popup__description" class="sui-description">
		<?php esc_html_e( 'Enter your Follow Up Boss API key (Follow Up Boss → Admin → API).', 'connectinator-real-estate-leads-for-forminator' ); ?>
	</p>
	<?php if ( ! empty( $ffub_vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $ffub_vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<div class="sui-form-field<?php echo esc_attr( ! empty( $ffub_vars['api_key_error'] ) ? ' sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'API Key', 'connectinator-real-estate-leads-for-forminator' ); ?></label>
		<div class="sui-control-with-icon">
			<input name="api_key" value="<?php echo esc_attr( $ffub_vars['api_key'] ); ?>"
				placeholder="<?php esc_attr_e( 'Enter your Follow Up Boss API key', 'connectinator-real-estate-leads-for-forminator' ); ?>"
				class="sui-form-control" autocomplete="off" />
			<i class="sui-icon-key" aria-hidden="true"></i>
		</div>
		<?php if ( ! empty( $ffub_vars['api_key_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $ffub_vars['api_key_error'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Lead source', 'connectinator-real-estate-leads-for-forminator' ); ?></label>
		<input name="source" value="<?php echo esc_attr( $ffub_vars['source'] ); ?>"
			placeholder="<?php esc_attr_e( 'Defaults to the form name', 'connectinator-real-estate-leads-for-forminator' ); ?>" class="sui-form-control" />
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'System name', 'connectinator-real-estate-leads-for-forminator' ); ?></label>
		<input name="system" value="<?php echo esc_attr( $ffub_vars['system'] ); ?>" class="sui-form-control" />
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Event type', 'connectinator-real-estate-leads-for-forminator' ); ?></label>
		<select name="event_type" class="sui-select">
			<?php foreach ( (array) $ffub_vars['event_types'] as $ffub_type ) : ?>
				<option value="<?php echo esc_attr( $ffub_type ); ?>" <?php selected( $ffub_vars['event_type'], $ffub_type ); ?>><?php echo esc_html( $ffub_type ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Tags', 'connectinator-real-estate-leads-for-forminator' ); ?></label>
		<input name="tags" value="<?php echo esc_attr( $ffub_vars['tags'] ); ?>"
			placeholder="<?php esc_attr_e( 'Comma-separated, applied to every contact', 'connectinator-real-estate-leads-for-forminator' ); ?>" class="sui-form-control" />
	</div>
</form>
