<!-- refer code length HTML -->
<input style="padding: .3em;" type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>"
name="wp_referral_code_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
value="<?php echo isset( $option[ $args['label_for'] ] ) ? esc_html( $option[ $args['label_for'] ] ) : 5; ?>">
<span style="color:red; font-weight: bold">
	<?php esc_html_e( 'Note: changing this option will reset refer code of all users', 'wp-referral-code' ); ?>
</span>
<!-- refer code length HTML -->
