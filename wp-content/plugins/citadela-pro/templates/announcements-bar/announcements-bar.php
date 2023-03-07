<div id="citadela-announcements-bar" class="<?php echo \ctdl\pro\class_attr( ["type-".esc_attr( $type ), 'button-present' => ( $button_text and $button_url ) ] ) ?>">
	<div class="bar-main-wrap">

		<?php if ( $type == 'simple' ): ?>

			<?php if ( $text ): ?>
				<div class="message-part">
					<p><?php echo $text ?></p>
				</div>
			<?php endif ?>

			<?php if ( $button_text and $button_url ): ?>
				<div class="button-part">
					<a href="<?php echo esc_url( $button_url ) ?>" class="button"><?php echo $button_text ?></a>
				</div>
			<?php endif; ?>

		<?php  elseif ( $type === 'advanced' and $html ): ?>

			<?php echo $html ?>
			
		<?php endif ?>

	</div>
</div>
