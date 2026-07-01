<?php
/**
 * Per-form: connection name step.
 *
 * @package ForminatorFollowUpBoss
 */

defined( 'ABSPATH' ) || exit;

$vars = array(
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
foreach ( $template_vars as $key => $val ) {
	$vars[ $key ] = $val;
}
?>

<div class="forminator-integration-popup__header">
	<h3 id="forminator-integration-popup__title" class="sui-box-title sui-lg" style="overflow: initial; white-space: normal; text-overflow: initial;"><?php esc_html_e( 'Set up a name', 'forminator-followup-boss' ); ?></h3>
	<p id="forminator-integration-popup__description" class="sui-description"><?php esc_html_e( 'Give this connection a friendly name so you can identify it.', 'forminator-followup-boss' ); ?></p>
	<?php if ( ! empty( $vars['error_message'] ) ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped.
		echo Forminator_Admin::get_red_notice( esc_html( $vars['error_message'] ) );
		?>
	<?php endif; ?>
</div>

<form>
	<div class="sui-form-field <?php echo esc_attr( ! empty( $vars['name_error'] ) ? 'sui-form-field-error' : '' ); ?>">
		<label class="sui-label"><?php esc_html_e( 'Name', 'forminator-followup-boss' ); ?></label>
		<input class="sui-form-control" name="name" value="<?php echo esc_attr( $vars['name'] ); ?>"
			placeholder="<?php esc_attr_e( 'Friendly name', 'forminator-followup-boss' ); ?>">
		<?php if ( ! empty( $vars['name_error'] ) ) : ?>
			<span class="sui-error-message"><?php echo esc_html( $vars['name_error'] ); ?></span>
		<?php endif; ?>
	</div>
	<input type="hidden" name="multi_id" value="<?php echo esc_attr( $vars['multi_id'] ); ?>">
</form>
