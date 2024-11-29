<label for="<?php echo esc_attr( $args['label_for'] ); ?>"></label>
<input type="checkbox"
id="<?php echo esc_attr( $args['label_for'] ); ?>"
name="wp_referral_code_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
value="1" <?php checked( isset( $option[ $args['label_for'] ] ) ? $option[ $args['label_for'] ] : 0, 1 ); ?>>
<br>
<small class="helper-text">Check this box to show referral info colums in the users table.</small>
