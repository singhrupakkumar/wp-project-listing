<div class="wp-block-citadela-blocks ctdl-item-extension <?php echo esc_attr( implode(' ', $classes) ); ?>">

    <?php if($blockTitle) : ?>
    <header class="citadela-block-header">
        <div class="citadela-block-title">
            <h2 <?php echo $title_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $blockTitle ); ?></h2>
        </div>
    </header>
    <?php endif; ?>

	<div class="citadela-block-articles">
		<div class="citadela-block-articles-wrap">

			<?php 
			foreach ($inputs as $key => $input) {
				$main_class = [ "input-{$key}", "type-{$input['type']}", $input['class'] ];
				?>
				<div class="data-row <?php echo esc_attr( implode(' ', $main_class ) );  ?>" <?php echo $wrapper_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="label" <?php echo $label_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><p><?php esc_html_e( $input['label'] ); ?></p></div>
					<div class="data" <?php echo $values_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php 
							if( $input['value'] ){
								if( $input['type'] === 'textarea' ) {
									echo wp_kses_post( $input['value'] );
								}else{
									echo wp_kses_post( '<p>'.$input['value'].'</p>' );
								}
							}else{
								echo '<p></p>';
							}
						?>
					</div>
					<?php if( $attributes['layout'] == 'text' ): ?>
						<span class="sep" <?php echo $separator_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></span>
					<?php endif; ?>
				</div>
				<?php
			}
			?>

		</div>
	</div>
	
</div>