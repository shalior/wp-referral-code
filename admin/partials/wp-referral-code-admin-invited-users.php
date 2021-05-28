<br>
<h2><?php esc_html_e( "WP Referral Code", 'wp-referral-code' ) ?></h2>

<table class="form-table">
    <tr>
        <th><?php esc_html_e( "Referral Code info", 'wp-referral-code' ); ?></th>
        <td>
            <!-- Lists  -->
            <br>
            <ul class="invited-users_list list">
				<?php

				/** @var WP_Refer_Code $ref_code */
				$referrer_id = $ref_code->get_referrer_id();
				if ( ! empty( $referrer_id ) ) {
					echo '<a href="' . admin_url( '/user-edit.php?user_id=' . $referrer_id ) . '" target="_blank">';
					_e( 'this user has been invited by', 'wp-referral-code' );
					echo get_user_meta( $referrer_id, 'first_name', true ) . " " .
					     get_user_meta( $referrer_id, 'last_name', true ) .
					     '</a><br><hr>';
				} else {
					_e( 'No one invited this user <br><hr>', 'wp-referral-code' );
				}

				$invited_users_ids = $ref_code->get_invited_users_id();
				_e( 'this user\'s invite link: ', 'wp-referral-code' );
				echo '<a href="' . $ref_code->get_ref_link() . '" target="_blank">' . $ref_code->get_ref_link() . '</a><br><hr>';

				if ( empty( $invited_users_ids ) ) {
					_e( 'this user has invited 0 users', 'wp-referral-code' );
				} else {
				?>
                <h4>This user has invited following users: </h4>
                <ul class="wp-referral-code-invited-users">
		            <?php
		            foreach ( $invited_users_ids as $invited_user_id ): ?>
                        <li class="invited-user-item item" id="<?php echo $invited_user_id ?>">
				            <?php
				            $invited_user = new WP_User( $invited_user_id );
				            echo '<a href="' . admin_url( '/user-edit.php?user_id=' . $invited_user_id ) . '" target="_blank">' . $invited_user->get( 'first_name' ) . " " . $invited_user->get( 'last_name' ) . "($invited_user->user_login)" . '</a>';
				            ?>
                            <button style="background-color: #dd382d; border-color: #dd382d"
                                    class="wrc-remove-relation button button-small button-primary delete-permanently"
                                    data-referrer-id="<?php esc_attr_e( $user_id ); ?>"
                                    data-user-id="<?php esc_attr_e( $invited_user_id ); ?>">Remove
                            </button>
                        </li>
		            <?php endforeach;
		            } ?>
                </ul>
            </ul>
        </td>
    </tr>
</table>
