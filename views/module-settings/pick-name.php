<?php
/**
 * Per-form: connection name step.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$ffub_vars = array(
	'error_message' => '',
	'name'          => '',
	'name_error'    => '',
	'multi_id'      => '',
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
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;"><?php esc_html_e( 'Set up a name', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></h3>
	<p id="forminator-integration-popup__description" class="sui-description"><?php esc_html_e( 'Give this connection a friendly name so you can identify it.', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></p>
	<?php if ( ! empty( $ffub_vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $ffub_vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<div class="sui-form-field <?php echo esc_attr( ! empty( $ffub_vars['name_error'] ) ? 'sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'Name', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?></label>
		<input class="sui-form-control" name="name" value="<?php echo esc_attr( $ffub_vars['name'] ); ?>"
			placeholder="<?php esc_attr_e( 'Friendly name', 'derintolu-lead-sync-follow-up-boss-forminator' ); ?>">
		<?php if ( ! empty( $ffub_vars['name_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $ffub_vars['name_error'] ); ?></span>
		<?php endif; ?>
	</div>
	<input type="hidden" name="multi_id" value="<?php echo esc_attr( $ffub_vars['multi_id'] ); ?>">
</form>
