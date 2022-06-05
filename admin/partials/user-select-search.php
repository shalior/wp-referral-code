<select id="wrc-search-user-select"
		class="wrc-search-user"
		style="width: 100%">
	<option value="-1"><?php esc_html_e( 'Search for a user', 'wp-referral-code' ); ?></option>
	<option value="-22"><?php esc_html_e( 'ddd', 'wp-referral-code' ); ?></option>
</select>

<script>
	jQuery(document).ready(function ($) {

		const $wrcSearchUser = $('#wrc-search-user-select');
		$wrcSearchUser.select2({
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						term: params.term,
						page: params.page || 1,
						action: 'wp_referral_code_search_user_select2',
						nonce: '<?php echo esc_html( wp_create_nonce( 'wp_referral_code_search_user_select2' ) ); ?>',
					};
				},
			},
			width: '300px',
			placeholder: '<?php esc_html_e( 'Search for a user by name or email', 'wp-referral-code' ); ?>',
		})
	});
</script>
