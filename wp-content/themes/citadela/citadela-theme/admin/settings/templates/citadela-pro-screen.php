<?php
$imgs_url = Citadela_Theme::get_instance()->theme_paths->url->settings . '/templates/img';
?>

<div class="citadela-dashboard">
	<div class="citadela-screen-header ctdl-active">
		<div class="citadela-screen-header-wrap">
			<div class="ctdl-brand">
				<h1><?php esc_html_e('Citadela Dashboard', 'citadela'); ?></h1>
				<p><?php esc_html_e('by AitThemes', 'citadela'); ?></p>
			</div>
			<div class="ctdl-brand-desc">
				<p><?php esc_html_e('Thank you for using premium Citadela plugins. As paying AitThemes customer you have access to our support forum and documentation.', 'citadela'); ?></p>
			</div>
		</div>
	</div>

	<div class="citadela-screen-holder ctdl-active ctdl-api-settings-active <?php echo Citadela::$package !== 'themeforest' && defined('CITADELA_PRO_PLUGIN') ? 'ctdl-pro-active' : ''; ?>">
		<div class="ctdl-screen-items">

        	<div class="ctdl-screen-item ctdl-status <?php echo (Citadela::$trial || !Citadela::$allowed) ? 'ctdl-trial' : 'ctdl-activated'; ?>">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">
						<h2 class="ctdl-item-title"><?php echo (Citadela::$trial || !Citadela::$allowed) ? esc_html_e('Citadela Activation', 'citadela') : esc_html_e('Citadela Activated', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php 
							echo wp_kses_post(Citadela::$trial ?
								sprintf(/* translators: 1. Start html anchor tag, 2. End html anchor tag  */ __('You can test a full version of premium Citadela features for free. For live website, you need an active membership and a valid API key for this domain. %1$sSee available memberships%2$s.', 'citadela'), '<a href="https://www.ait-themes.club/pricing/">', '</a>') :
								sprintf(/* translators: %s - activation key type: Purchase Code or API Key */ __('You need to enter %1$s in order to use premium Citadela features. One %1$s will allow you to run Citadela on one URL. It will also ensure automatic updates for theme and plugins.', 'citadela'), in_array(Citadela::$package, ['themeforest', 'mojo', 'themely']) ? __('Purchase Code', 'citadela') : __('API Key', 'citadela'))
							); ?></p>
					</div>
				</div>
			</div>
			
			<div class="ctdl-screen-item ctdl-api <?php echo esc_attr(Citadela::$package === 'themeforest' ? 'tf-purchase' : '') ?>">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">
						<div class="ctdl-item-settings"><?php do_action( 'citadela_updater_options' ); ?></div>
					</div>
				</div>
			</div>
			
			<?php if (Citadela::$package !== 'themeforest' && defined('CITADELA_PRO_PLUGIN')) { ?>

			<div class="ctdl-screen-item ctdl-layouts">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">
						<h2 class="ctdl-item-title"><?php esc_html_e('Citadela Layouts', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('With our Citadela WordPress theme layouts, you don’t need to hire a designer to help you make your website look professional. Our designers have already made that for you. Citadela WordPress Layouts are carefully designed to suit your business. All you need is to change the content to present your work. It saves you time and money.', 'citadela'); ?></p>
						<p class="ctdl-item-cta"><a href="<?php echo esc_url( admin_url( "admin.php?page=citadela-pro-settings&tab=layouts" ) ) ?>"><?php esc_html_e('Explore Citadela Layouts', 'citadela'); ?></a></p>
					</div>
				</div>
			</div>

			<?php } ?>
			
			<div class="ctdl-screen-item ctdl-doc">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">
						<h2 class="ctdl-item-title"><?php esc_html_e('Helpful Documentation', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('Citadela documentation includes everything you need to understand how theme and premium plugins work. It is written for you and other users to get to know our theme as quickly as possible. Documentation is updated on daily basis. Includes description of new features and frequently asked questions from our support.', 'citadela'); ?></p>
						<p class="ctdl-item-cta"><a href="https://www.ait-themes.club/citadela-documentation/" target="_blank"><?php esc_html_e('Start Reading', 'citadela'); ?></a></p>
					</div>
				</div>
			</div>

			<div class="ctdl-screen-item ctdl-support">
				<div class="ctdl-screen-body">
					<div class="ctdl-screen-content">
						<h2 class="ctdl-item-title"><?php esc_html_e('Customer Support', 'citadela'); ?></h2>
						<p class="ctdl-item-desc"><?php esc_html_e('Trained support team will help you to start working on your website quickly. Our goal is to teach you how to use our products efficiently and the right way. Support system is fully confidential and closed, you can ask there any question regarding our theme you like. Please bear in mind that we do not do any customizations. There are plenty of location designers that will be happy to help you with your custom ideas.', 'citadela'); ?></p>
						<?php
							switch (Citadela::$package) {
								case 'themeforest':
									$urlSupport =  Citadela::$url . '/join/tf';
									break;
								case 'mojo':
									$urlSupport =  Citadela::$url . '/join/mojo';
									break;
								case 'themely':
									$urlSupport =  Citadela::$url . '/join/themely';
									break;
								default:
									$urlSupport =  Citadela::$url . '/support';
									break;
							}
						?>
						<p class="ctdl-item-cta"><a href="<?php echo esc_attr($urlSupport); ?>" target="_blank"><?php esc_html_e('Visit Support', 'citadela'); ?></a></p>
					</div>
				</div>
			</div>

		</div>
	</div>

</div>