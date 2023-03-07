<?php

namespace Citadela\Pro\Announcements_Bar;

use Citadela\Pro\Template;

class Feature {

	function __construct() {
		add_action( 'wp_print_styles', [ $this, 'print_styles' ] );
		add_action( 'wp_body_open', [ $this, 'display_on_frontend' ] );
	}



	function print_styles() {
		if ( $this->should_display() && $this->settings()->value('type') !== 'advanced' ) return;
		?>
		<style>
			<?php echo $this->settings()->value( 'css' ) ?>
		</style>
		<?php
	}



	function display_on_frontend() {
		if ( ! $this->should_display() ) return;
		Template::load( '/announcements-bar/announcements-bar', $this->settings()->values() );
	}



	protected function should_display() {
		if( is_customize_preview() ) {
			return $this->should_display_in_customizer();
		}else{
			if( is_admin() or ! $this->settings()->value( 'enable' ) ) {
				return false;
			}

			$from = $this->settings()->value( 'date_from' );
			$to = $this->settings()->value( 'date_to' );
			$now = current_time('timestamp');

			if ( empty( $from) and empty( $to ) ) {
				return true;
			}

			if( empty( $from ) and ! empty( $to ) ) {
				return ( $now <= $to );
			}

			if( ! empty( $from ) and empty( $to ) ) {
				return ( $now >= $from );
			}

			return ( $now >= $from and $now <= $to );
		}
	}



	protected function should_display_in_customizer() {
		return get_theme_mod( 'citadela_setting_announcementsBarShowInCustomizer', 0 ) ? true : false ;
	}



	function settings() {
		static $settings = null;
		if ( ! $settings ) {
			$settings = new Settings;
		}
		return $settings;
	}

}
