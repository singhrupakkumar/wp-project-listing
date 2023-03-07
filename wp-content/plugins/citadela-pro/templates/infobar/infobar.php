<div
	id="citadela-infobar"
	class="<?php echo \ctdl\pro\class_attr( [ 'button-present' => !! $button_text, "position-{$position}" ] ) ?>"
	data-cexp="<?php echo esc_attr( $expiration ) ?>"
	style="display:none"
>
	<div class="main-wrap">
		<div class="<?php echo \ctdl\pro\class_attr( [ 'text-part', 'no-btn-text' => ! $button_text ] ) ?>">
			<?php echo $text; ?>
		</div>
		<?php if ( $button_text ): ?>
		<div class="button-part">
			<div class="button"><?php echo esc_html( $button_text ) ?></div>
		</div>
		<?php endif ?>
	</div>
	<?php if( ! $button_text ): ?>
		<span class="button simple-close-button"></span>
	<?php endif ?>
</div>
