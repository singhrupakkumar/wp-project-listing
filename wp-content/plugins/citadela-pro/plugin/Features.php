<?php

namespace Citadela\Pro;

class Features {

	public static $features = [];
	public static $settings = [];


	static function register() {
		self::$features = apply_filters('citadela\pro\features', [
			new Integrations\Feature,
			new Infobar\Feature,
			new Comments_Extension\Feature,
			new Announcements_Bar\Feature,
            new Layouts\Download\Feature,
            new Layouts\Upload\Feature,
			new Layout_Exporter\Feature,
            new Special_Pages\Feature,
			new Blocks\Feature,
			new Custom_Header\Feature,
			new Content_Settings\Feature,
			new Half_Layout\Feature,
			new Events\Feature
		]);
		foreach( self::$features as $feature ) {
			if ( method_exists( $feature, 'settings' ) and ! empty( $feature->settings() ) ) {
				self::$settings[ $feature->settings()->slug() ] = $feature->settings();
			}
		}
	}



	static function settings( $slug = null) {
		if ( ! $slug ) {
			return self::$settings;
		}
		return self::$settings[ $slug ];
	}

}
