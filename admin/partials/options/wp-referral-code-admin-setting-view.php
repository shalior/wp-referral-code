<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting.
		settings_fields( 'wp-referral-code' );
		// output setting sections and their fields.
		do_settings_sections( 'wp-referral-code' );
		// output save settings button.
		submit_button( __( 'Save Settings', 'wp-referral-code' ) );
		?>
	</form>

</div>

<div class="wrap">
	<style>
		td.wrc-shortcode {
			padding: 1em;
			font-weight: bold;
		}
	</style>
	<table>
		<thead>
		<h1><?php esc_html_e( 'Available shortcodes', 'wp-referral-code' ); ?></h1>
		</thead>
		<tbody>
		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="copy_ref_link"]
			</td>
			<td>
				<?php esc_html_e( 'Shows a user friendly box to copy the refer code to the current user', 'wp-referral-code' ); ?>
			</td>
		</tr>

		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="ref_code"]
			</td>
			<td>
				<?php esc_html_e( 'Returns refer code of current user', 'wp-referral-code' ); ?>
			</td>
		</tr>

		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="ref_link"]
			</td>
			<td>
				<?php esc_html_e( 'Returns refer link specific to current user (register link + refer code)', 'wp-referral-code' ); ?>
			</td>
		</tr>

		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="invited_count"]
			</td>
			<td>
				<?php esc_html_e( 'Returns number of users current user invited (number of users who have used current users refer code)', 'wp-referral-code' ); ?>
			</td>
		</tr>

		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="invited_list"]
			</td>
			<td>
				<?php esc_html_e( 'Shows a list of users current user invited (lists usernames by default: use hooks to change it)', 'wp-referral-code' ); ?>
			</td>
		</tr>

		<tr>
			<td class="wrc-shortcode">
				[wp-referral-code var="most_referring_users"]
			</td>
			<td>
				<?php esc_html_e( 'Shows a list of top referring users (10 users by default, configurable with hooks)', 'wp-referral-code' ); ?>
			</td>
		</tr>
		</tbody>
	</table>
</div>
