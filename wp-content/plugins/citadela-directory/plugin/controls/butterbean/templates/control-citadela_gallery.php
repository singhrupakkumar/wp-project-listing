<?php 

// ===============================================
// Citadela Gallery Control functions
// -----------------------------------------------

global $post;
$meta = get_post_meta($post->ID, '_citadela_gallery_images', true);
$gallery_data = $meta ? $meta : [];
?>

<div class="citadela-control">
	<div {{{ data.attr }}}>
		<div class="option-field">

				<# if ( data.label ) { #>
				<label>
					<span class="butterbean-label">{{ data.label }}</span>
				</label>
				<# } #>

				<div class="input-wrapper">
					<div class="gallery-images" data-caption-label="<?php esc_html_e( 'Caption text', 'citadela-directory' ); ?>">
					<?php
					if( !empty( $gallery_data ) ) {
						foreach ($gallery_data as $key => $data) {
							$id = is_array($data) ? $data['id'] : $data;
							$caption =  isset( $data['caption'] ) ? $data['caption'] : '';

							$src = wp_get_attachment_image_src( $id, 'thumbnail' );
							if( isset( $src[0] ) && $src[0] ) {
								$url = $src[0];
							?>
								<div class="image-wrapper">
									<div class="image">
										<input type="hidden" name="{{{ data.field_name }}}[<?php esc_attr_e($key); ?>][id]" value="<?php esc_attr_e( $id ); ?>">
										<img src="<?php echo esc_url($url); ?>"/>
										<div class="caption">
											<label><?php esc_html_e( 'Caption text', 'citadela-directory' ); ?></label>
											<input type="text" name="{{{ data.field_name }}}[<?php esc_attr_e($key); ?>][caption]" value="<?php esc_attr_e( $caption ); ?>">
										</div>
									</div>
									<div class="delete"></div>
								</div>
							<?php
							}
						}
					}
					?>
					</div>

					<input type="button" class="button button-primary add-media" value="<?php esc_html_e( 'Add images to gallery', 'citadela-directory'); ?>">
				</div>
				
				<# if ( data.description ) { #>
					<span class="butterbean-description">{{{ data.description }}}</span>
				<# } #>

		</div>

	</div>
</div>