<?php

namespace Citadela\Pro;

class Settings_Page {

	static function slug() {
		return 'citadela-pro-settings';
	}



	static function init() {
		if( ! is_admin() ) return;

		add_action( 'admin_menu', [ __CLASS__, 'menu' ] );
		add_action( "admin_enqueue_scripts", [__CLASS__, 'admin_enqueue' ], 15 );
	}



	static function menu() {
		$hook = add_menu_page(
			'Citadela Pro &sdot; ' . self::current_tab()->label,
			'Citadela Pro',
			'manage_options',
			self::slug(),
			[ __CLASS__, 'content' ],
			Icon::data_url(),
			25 //after Comments menu
		);

		add_action( "admin_head-{$hook}", [ __CLASS__, 'head' ] );
	}



	static function head() {
		// add settings slug as admin body class
		add_filter( 'admin_body_class', [ __CLASS__, 'body_class' ] );
	}


	static function admin_enqueue() {
		// assets enqueued just for current settings page tab
		if ( method_exists( self::current_settings(), 'admin_enqueue' ) ) {
			self::current_settings()->admin_enqueue();
		}
	}



	static function content() {
		Template::insert_data( '/_settings-navigation', [
			'tabs' => self::tabs(),
			'current_tab' => self::current_tab_slug(),
		] );

		self::current_settings()->display();
	}



	static function current_settings() {
		return Features::settings( self::current_tab_slug() );
	}



	static function current_tab_slug() {
		return (
			( ! empty( $_GET['tab'] ) and isset( self::tabs()[ $_GET['tab'] ] ) )
			? $_GET['tab']
			: self::default_tab_slug()
		);
	}



	static function default_tab_slug() {
		foreach( self::tabs() as $slug => $tab ){
			if ( ! empty( $tab->default ) ) {
				return $slug;
			}
		}
	}



	static function current_tab() {
		return self::tabs()[ self::current_tab_slug() ];
	}



	static function tabs() {
		static $tabs = [];
		if ( empty( $tabs ) ) {
			foreach( Features::settings() as $settings ) {
				$tabs[ $settings->slug() ] = (object) $settings->tab();
				$tabs[ $settings->slug() ]->url = add_query_arg( 'tab', $settings->slug(), admin_url( 'admin.php?page=' . self::slug() ) );
			}
		}
		return $tabs;
	}



	static function body_class( $classes ){
		return $classes . ' citadela-settings-page ' . self::slug();
	}
}
