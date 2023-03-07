<?php
	$imgs_url = Citadela_Theme::get_instance()->theme_paths->url->settings . '/templates/img';
	$package = class_exists('Citadela') ? Citadela::$package : '';
	$url = class_exists('Citadela') ? Citadela::$url : 'https://system.ait-themes.club';
	$layouts = json_decode(wp_remote_get($url . '/core/products?parameters={"where":{"citadela_layout":1}}')['body'], true);
?>

<div class="citadela-screen-section ctdl-section-space">
	<h2 class="citadela-screen-title"><?php esc_html_e('Ready to use Citadela layouts', 'citadela'); ?></h2>
	<?php if ($package === 'themeforest') { ?>
		<p class="citadela-screen-subtitle"><?php esc_html_e('You can import any of these layouts to start building your website. Layout can be imported using Citadela Pro plugin. Layout packages can be found in the main zip file you can download from Themeforest.', 'citadela'); ?></p>
	<?php } else { ?>
		<p class="citadela-screen-subtitle"><?php esc_html_e('You can import any of these layouts to start building your website. Layout can be imported using Citadela Pro plugin.', 'citadela'); ?></p>
	<?php } ?>
</div>

<div class="citadela-screen-holder ctdl-layouts">
	<div class="ctdl-screen-items">
		<?php foreach($layouts as $layout) { ?>
		<div class="ctdl-screen-item">
			<?php if ($package !== 'themeforest') { ?>
			<a href="https://www.ait-themes.club/citadela-layouts/" target="_blank">
			<?php } ?>
			<div class="ctdl-screen-body">
				<div class="ctdl-screen-thumb">
					<img src="<?php echo esc_attr($url . $layout['thumbnail_link']); ?>" alt="<?php echo esc_attr($layout['display_name']); ?>">
				</div>
				<div class="ctdl-screen-content">
					<h2 class="ctdl-item-title"><?php echo esc_html($layout['display_name']); ?></h2>
					<p class="ctdl-item-subtitle"><?php echo esc_html(str_replace('Citadela ', '', $layout['display_name']) . ' ' .  __( 'Layout Pack', 'citadela'));  ?></p>
				</div>
			</div>
			<?php if ($package !== 'themeforest') { ?>
			</a>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>

<?php if ($package !== 'themeforest') { ?>
<div class="citadela-screen-section">
	<p class="ctdl-item-cta"><a href="https://www.ait-themes.club/citadela-layouts/" target="_blank"><?php esc_html_e('Download layouts', 'citadela'); ?></a></p>
</div>
<?php } ?>