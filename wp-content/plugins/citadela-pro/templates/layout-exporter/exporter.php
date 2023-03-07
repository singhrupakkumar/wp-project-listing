<?php
use Citadela\Pro\Template;
use Citadela\Pro\Layout_Exporter\Plugin;

$disabled_feature = \ctdl\pro\dot_get( get_option( 'citadela_pro_integrations' ), 'disable_layout_import_export' );

?>

<div class="wrap citadela-settings-wrap">
	<?php Template::load('/_settings-header'); ?>
		<div class="citadela-settings-content">
			<?php Template::load('/_settings-navigation'); ?>
			<div class="citadela-settings tab-layout_exporter"><?php
				if( $disabled_feature ) : 
					do_action('ctdl_disable_layout_import_export_content');
				else : 
					?>
					
					<div id="citadela-layout-exporter-root" class="exporter-containter">
						<div class="exporter-containter-wrap">
							<p><?php esc_html_e('Layout Exporter feature exports WordPress pages, posts and all Citadela options and settings. You can use Layout Exporter to create a Citadela layout that you can easily install to another Citadela website.', 'citadela-pro'); ?></p>
							<p><?php esc_html_e('Please understand that Layout Exporter does not export everything from WordPress, such as WooCommerce invoices or data from other 3rd party plugins. There are many other plugins available for migration purposes.', 'citadela-pro'); ?></p>
							<div class="confirm-buttons">
								<a href="<?php echo Plugin::download_url() ?>" class="button button-hero button-primary">
									<?php 
									// translators: %s site name
									printf(__('Export %s Layout', 'citadela-pro'), '<strong>' . ucfirst(basename(site_url())) . '</strong>'); 
									?>
								</a>
							</div>
							<div class="mt-6 text-gray-600">
								<?php echo Plugin::exporter()->zip_name(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
</div>

<?php
add_action( 'admin_print_footer_scripts', function(){
	ob_start();
	?>
		<style>
			.wp-core-ui #citadela-layout-exporter-root { font-size: 14px; min-height: calc(100vh - 65px - 32px); }
			.wp-core-ui .flex { display: flex; }
			.wp-core-ui .items-center { align-items: center; }
			.wp-core-ui .justify-center { justify-content: center; }
			.wp-core-ui .text-center { text-align: center; }
			.wp-core-ui .mt-6 { margin-top: 1.5rem; }
			.wp-core-ui .text-gray-500 { color: #a0aec0; }
			.wp-core-ui .text-gray-600 { color: #718096; }
			.button { position: relative; }
		</style>
	<?php
	echo ob_get_clean();
} );