<?php

// ===============================================
// Citadela Listing Google reCaptcha function
// -----------------------------------------------


class CitadelaDirectoryRecaptcha {

	public static $plugin, $siteKey, $secretKey;
	public static $activeCaptcha;

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function init(){
		self::$plugin = CitadelaDirectory::getInstance();
		self::$siteKey = self::get_sitekey();
		self::$secretKey = self::get_secretkey();
			
		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'recaptcha_enqueue_scripts'), 11, 0 );
		

	}

	public static function recaptcha_enqueue_scripts(){
		self::$activeCaptcha = $activeCaptcha = self::shouldRunCaptcha();
		if(!$activeCaptcha) return;

		//we can run recaptcha
		$url = add_query_arg(
			array(
				'render' => self::$siteKey,
			),
			'https://www.google.com/recaptcha/api.js'
		);

		wp_enqueue_script( 'citadela-google-recaptcha', $url, array(), '3.0', true );
	}

	private static function shouldRunCaptcha(){
		//check if we are on general item detail page
		if( !is_singular('citadela-item') ) return false;
		
		//check if keys and contact form block are present
		if( self::$siteKey === false || self::$secretKey === false || !CitadelaDirectoryLayouts::itemPageHasBlock('item-contact-form') ) return false;
		
		//check if the form has to be used on currently opened item page
		global $post;
		$meta = CitadelaDirectoryFunctions::getItemMeta($post->ID);
		$enabledForm = ( $meta->use_contact_form && $meta->email ) ? true : false;
		if(!$enabledForm) return false;
		
		return true;
	}


	public static function verify( $token ){
		$verifyurl = 'https://www.google.com/recaptcha/api/siteverify';
		$request = array(
			'body' => array(
				'secret' => self::$secretKey,
				'response' => $token,
			),
		);
		$response = wp_remote_post( esc_url_raw( $verifyurl ), $request );
		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response );
		return $response;
	}

	public static function get_sitekey(){
		$key = self::$plugin->pluginOptions->general['google_recaptcha_site_key'];
		return $key ? $key : false;
	}

	public static function get_secretkey(){
		$key = self::$plugin->pluginOptions->general['google_recaptcha_secret_key'];
		return $key ? $key : false;
	}

}