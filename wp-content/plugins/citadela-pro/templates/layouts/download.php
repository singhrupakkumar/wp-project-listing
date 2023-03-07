<?php
use Citadela\Pro\Template;

$disabled_feature = \ctdl\pro\dot_get( get_option( 'citadela_pro_integrations' ), 'disable_layout_import_export' );

?>

<div class="wrap citadela-settings-wrap">
	<?php Template::load('/_settings-header'); ?>
	<div class="citadela-settings-content">
		<?php Template::load('/_settings-navigation'); ?>
		<div class="citadela-settings tab-layouts"><?php
			if( $disabled_feature ) :
				do_action('ctdl_disable_layout_import_export_content');
			else :
				Template::load('layouts/progress', ['type' => 'download']);
				?>
				<div id="citadela-layouts-root">
					<div class="citadela-screen-section">
						<h2 class="citadela-screen-title"><?php esc_html_e('Ready to use Citadela layouts', 'citadela-pro'); ?></h2>
						<p class="citadela-screen-subtitle"><?php esc_html_e('You can import any of these layouts to start building your website. Please bear in mind that importing layout will delete all your current pages and posts.'); ?></p>
					</div>
					<div id="citadela-layouts" class="citadela-screen-holder ctdl-layouts">
						<div class="ctdl-screen-items">
							<?php foreach ($layouts as $layout) { ?>
							<div class="ctdl-screen-item">
								<div class="ctdl-screen-body">
									<div class="ctdl-screen-thumb">
										<img src="<?php echo esc_attr($url . $layout['thumbnail_link']); ?>" alt="<?php echo esc_attr($layout['display_name']); ?>">
									</div>
									<div class="ctdl-screen-content">
										<h2 class="ctdl-item-title"><?php echo esc_html($layout['display_name']); ?></h2>
										<a href="<?php echo esc_attr($layout['preview_link']); ?>" target="_blank"><?php esc_html_e( 'View Demo', 'citadela-pro' ) ?></a>
										<a class="citadela-import-layout" href="<?php echo esc_attr(admin_url('admin-ajax.php') . '?' . http_build_query(['action' => 'citadela-pro-layout-download', 'layout' => $layout['code_name']])); ?>"><?php esc_html_e('Import Layout', 'citadela-pro') ?></a>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>