<table class="wrc-most-referring-table">
	<thead class="wrc-most-referring-thead">
	<tr>
		<td>User</td>
		<td>Count</td>
	</tr>
	</thead>
	<?php foreach ( $results as $result ) : ?>
	<tbody class="wrc-most-referring-tbody">
		<tr>
			<?php $user = get_user_by( 'id', $result['id'] ); ?>
			<td><?php echo esc_html( apply_filters( 'wp_referral_code_most_referring_user_text', $user->user_login, $result['id'] ) ); ?></td>
			<td><?php echo esc_html( $result['counted'] ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
