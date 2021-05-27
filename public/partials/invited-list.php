<?php if ( ! empty( $invited_users ) ): ?>
    <ul class="wrc-invited-users">
		<?php foreach ( $invited_users as $user_id ): ?>
			<?php
			$user = get_user_by( 'id', $user_id );
			if ( false !== $user ): ?>
                <li class="wrc-invited-user-item" id="wrc-invited-user-<?php esc_attr_e( $user_id ); ?>">
					<?php esc_html_e( apply_filters( 'wp_referral_code_invited_user_text', $user->user_login, $user_id ) ) ?>
                </li>
			<?php endif; ?>

		<?php endforeach; ?>
    </ul>
<?php else: ?>
	<?php $message = __( 'You have invited no one yet!', 'wp-referral-code' ); ?>
    <p class="wrc-empty-invite-list-message">
		<?php esc_html_e( apply_filters( 'wp_referral_code_empty_list_message', $message, get_current_user_id() ) ); ?>
    </p>
<?php endif; ?>