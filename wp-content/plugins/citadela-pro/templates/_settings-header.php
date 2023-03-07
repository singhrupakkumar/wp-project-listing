<?php
use Citadela\Pro\Icon;
?>

<h1>
	<?php echo Icon::html() ?>
	<?php echo esc_html( get_admin_page_title() ); ?>
</h1>

<?php 
if ( isset( $_GET['settings-updated'] ) ) {
	// add settings saved message with the class of "updated"
	add_settings_error( 'citadela_settings_message', 'citadela_settings_message', esc_html__( 'Settings Saved', 'citadela-pro' ), 'updated' );
}
settings_errors( 'citadela_settings_message' );
?>