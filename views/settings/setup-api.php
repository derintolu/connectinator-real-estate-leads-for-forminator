<?php
/**
 * Follow Up Boss connect screen (global settings).
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$vars = array(
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
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}
?>

<div class="forminator-integration-popup__header">
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;">
		<?php esc_html_e( 'Connect Follow Up Boss', 'lead-sync-for-follow-up-boss-forminator' ); ?>
	</h3>
	<p id="forminator-integration-popup__description" class="sui-description">
		<?php esc_html_e( 'Enter your Follow Up Boss API key (Follow Up Boss → Admin → API).', 'lead-sync-for-follow-up-boss-forminator' ); ?>
	</p>
	<?php if ( ! empty( $vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<div class="sui-form-field<?php echo esc_attr( ! empty( $vars['api_key_error'] ) ? ' sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'API Key', 'lead-sync-for-follow-up-boss-forminator' ); ?></label>
		<div class="sui-control-with-icon">
			<input name="api_key" value="<?php echo esc_attr( $vars['api_key'] ); ?>"
				placeholder="<?php esc_attr_e( 'Enter your Follow Up Boss API key', 'lead-sync-for-follow-up-boss-forminator' ); ?>"
				class="sui-form-control" autocomplete="off" />
			<i class="sui-icon-key" aria-hidden="true"></i>
		</div>
		<?php if ( ! empty( $vars['api_key_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['api_key_error'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Lead source', 'lead-sync-for-follow-up-boss-forminator' ); ?></label>
		<input name="source" value="<?php echo esc_attr( $vars['source'] ); ?>"
			placeholder="<?php esc_attr_e( 'Defaults to the form name', 'lead-sync-for-follow-up-boss-forminator' ); ?>" class="sui-form-control" />
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'System name', 'lead-sync-for-follow-up-boss-forminator' ); ?></label>
		<input name="system" value="<?php echo esc_attr( $vars['system'] ); ?>" class="sui-form-control" />
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Event type', 'lead-sync-for-follow-up-boss-forminator' ); ?></label>
		<select name="event_type" class="sui-select">
			<?php foreach ( (array) $vars['event_types'] as $type ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $vars['event_type'], $type ); ?>><?php echo esc_html( $type ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="sui-form-field">
		<label class="sui-label"><?php esc_html_e( 'Tags', 'lead-sync-for-follow-up-boss-forminator' ); ?></label>
		<input name="tags" value="<?php echo esc_attr( $vars['tags'] ); ?>"
			placeholder="<?php esc_attr_e( 'Comma-separated, applied to every contact', 'lead-sync-for-follow-up-boss-forminator' ); ?>" class="sui-form-control" />
	</div>
</form>
