<?php

$item_website_rel = isset( $item_detail_options['item_website_rel'] ) ? $item_detail_options['item_website_rel'] : 'nofollow';

?>
<div class="wp-block-citadela-blocks ctdl-item-contact-details <?php echo esc_attr( implode( " ", $classes ) );?>">

    <?php if($blockTitle) : ?>
    <header class="citadela-block-header">
        <div class="citadela-block-title">
            <h2><?php echo esc_html( $blockTitle ); ?></h2>
        </div>
    </header>
    <?php endif; ?>


	<div class="citadela-block-articles">
		<div class="citadela-block-articles-wrap">

			<?php if($meta->address): ?>
			<div class="cd-info cd-address" itemscope itemtype="http://schema.org/PostalAddress">
				<div class="cd-label"><p><?php esc_html_e('Address', 'citadela-directory'); ?></p></div>
				<div class="cd-data" itemprop="streetAddress">
					<p><?php echo esc_html( $meta->address ); ?></p>
				</div>
			</div>
			<?php endif; ?>


			<?php if( ( $meta->latitude === "0" && $meta->longitude === "0" ) != true
					&& ( $meta->latitude === "" && $meta->longitude === "" ) != true  ): ?>
			<div class="cd-info cd-gps" itemscope itemtype="http://schema.org/Place">
				<div class="cd-label"><p><?php esc_html_e('GPS', 'citadela-directory'); ?></p></div>
				<div class="cd-data" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
					<p>
						<?php echo esc_html( $meta->latitude.', '.$meta->longitude ); ?>
						<meta itemprop="latitude" content="<?php echo esc_attr( $meta->latitude ); ?>">
						<meta itemprop="longitude" content="<?php echo esc_attr( $meta->longitude ); ?>">
					</p>
				</div>
			</div>
			<?php endif; ?>


			<?php if($meta->telephone): ?>
			<div class="cd-info cd-phone">
				<div class="cd-label"><p><?php esc_html_e('Telephone', 'citadela-directory'); ?></p></div>
				<div class="cd-data">
					<p itemprop="telephone"><a href="tel:<?php echo esc_attr( str_replace(' ', '', $meta->telephone) ); ?>" class="phone"><?php echo esc_html( $meta->telephone ); ?></a></p>
				</div>
			</div>
			<?php endif; ?>


			<?php if( $meta->email && $meta->show_email ): ?>
			<div class="cd-info cd-mail">
				<div class="cd-label"><p><?php esc_html_e('Email', 'citadela-directory'); ?></p></div>
				<div class="cd-data">
					<p><a href="mailto:<?php echo esc_attr( $meta->email ); ?>" target="_top" itemprop="email"><?php echo esc_html( $meta->email ); ?></a></p>
				</div>
			</div>
			<?php endif; ?>


			<?php if($meta->web_url): ?>
			<div class="cd-info cd-web">
				<div class="cd-label"><p><?php esc_html_e('Web', 'citadela-directory'); ?></p></div>
				<div class="cd-data">
					<p><a href="<?php echo esc_url( $meta->web_url ); ?>" target="_blank" itemprop="url" <?php  if( $item_website_rel != "dofollow" ) echo 'rel="' . esc_attr( $item_website_rel ) . '"'; ?>><?php echo esc_html( ( $meta->web_url_label ) ?  $meta->web_url_label : $meta->web_url ); ?></a></p>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>