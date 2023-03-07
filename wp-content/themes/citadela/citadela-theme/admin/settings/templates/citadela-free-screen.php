<?php
$imgs_url = Citadela_Theme::get_instance()->theme_paths->url->settings . '/templates/img';
?>

<div class="citadela-dashboard">
	<div class="citadela-screen-header ctdl-free">
		<div class="citadela-screen-header-wrap">
			<div class="ctdl-intro">
				<h1><?php esc_html_e('Thank you for installing Citadela Theme by AitThemes', 'citadela'); ?></h1>
				<p class="ctdl-main-desc"><?php esc_html_e('Citadela is a free multi-purpose WordPress theme. You can use it without any restrictions on your commercial or non-commercial website. However, our premium plugins can shift your website to the whole new level!', 'citadela'); ?></p>
			</div>
		</div>
	</div>
	
	<div class="citadela-screen-holder ctdl-free">
		<div class="ctdl-screen-items">

        	<div class="ctdl-screen-item ctdl-install">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">

                    	<span class="ctdl-item-label"><?php esc_html_e('Plugin', 'citadela'); ?></span>
                    	<div class="ctdl-install-screen">
                    		<div class="ctdl-install-button">
                    			<a class="citadela-installation" href="<?php echo admin_url('admin-ajax.php?action=citadela-installation'); ?>"><?php esc_html_e('Install Citadela Plugins', 'citadela'); ?></a>
								<div class="citadela-installation-notice citadela-installation-progress"><?php _e('Installing...', 'citadela'); ?></div>
								<div class="citadela-installation-notice citadela-installation-error"><?php _e('Error with installing', 'citadela'); ?></div>
								<div class="citadela-installation-notice citadela-installation-success"><?php _e('Installed', 'citadela'); ?></div>
                    		</div>
							<p class="ctdl-item-desc"><?php esc_html_e('You can test a full version of premium Citadela features for free.', 'citadela'); ?></p>
						</div>

					</div>
				</div>
			</div>
			
        	<div class="ctdl-screen-item ctdl-pro-info">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">

                    	<span class="ctdl-item-label"><?php esc_html_e('Premium Plugin', 'citadela'); ?></span>
						<h2 class="ctdl-item-title"><?php esc_html_e('Citadela Pro', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('Customize colors, fonts and layout. Adds custom made blocks for Gutenberg WordPress editor.', 'citadela'); ?></p>
						<span class="ctdl-info-icon"></span>

					</div>
				</div>
			</div>

			<div class="ctdl-screen-item ctdl-dir-info">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">

						<span class="ctdl-item-label"><?php esc_html_e('Premium Plugin', 'citadela'); ?></span>
						<h2 class="ctdl-item-title"><?php esc_html_e('Citadela Listing', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('Adds listing Gutenberg blocks & features such as Map, GPX, blog posts on map or subscriptions.', 'citadela'); ?></p>
						<span class="ctdl-info-icon"></span>

					</div>
				</div>
			</div>
			
			<div class="ctdl-screen-item ctdl-layouts-info">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">

						<span class="ctdl-item-label"><?php esc_html_e('Premium Feature', 'citadela'); ?></span>
						<h2 class="ctdl-item-title"><?php esc_html_e('Citadela Layouts', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('Ready-to-use one click install Citadela layouts to start your new website in minutes.', 'citadela'); ?></p>
						<span class="ctdl-info-icon"></span>

					</div>
				</div>
			</div>

		</div>
	</div>

</div>
