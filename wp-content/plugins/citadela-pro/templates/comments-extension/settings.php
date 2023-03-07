<?php
use Citadela\Pro\Template;
?>

<div class="wrap citadela-settings-wrap">
	<?php Template::load( '/_settings-header' ) ?>
	<div class="citadela-settings-content">
		<?php Template::load( '/_settings-navigation' ) ?>
		<?php $settings->do_form(); ?>
	</div>
</div>
