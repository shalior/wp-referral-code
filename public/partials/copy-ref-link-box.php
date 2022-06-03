<?php
/**
 * Copy link shortcode view
 *
 * @var WP_Refer_Code $ref_code class instance
 * @package           WP_Referral_Code
 */

?>
<div class="input-group">
					<span class="input-group-button">
	<button id="copy-btn" class="coping-btn" data-clipboard-text="
	<?php
	echo esc_url( $ref_code->get_ref_link() )
	?>
	">
		<img src="<?php echo esc_attr( plugins_url( '', __FILE__ ) . '/copy.png' ); ?>" alt="<?php esc_attr_e('Copy icon', 'wp-referral-code' ); ?>">
	</button>

	</span>
	<div class="tooltip"><span class="tooltiptext"><?php esc_html_e( 'Tooltip text', 'wp-referral-code' ); ?></span>
		<input id="ref_link" readonly type="text" value="
		<?php
		echo esc_url( $ref_code->get_ref_link() )
		?>
		"></div>

</div>
