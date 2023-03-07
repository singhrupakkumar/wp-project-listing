<?php
/**
 * Citadela Listing Settings - General page
 *
 */

class CitadelaDirectorySettingsGeneral extends CitadelaDirectorySettings {

	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function create(){

		self::createSections();

		$settings = self::settings();
		CitadelaDirectorySettings::addSettings($settings);

	}

	private static function createSections(){

		add_settings_section(
			'citadela_section_google_maps',	// section id
			esc_html__('Google Maps API Key', 'citadela-directory'),							// section title
			array('CitadelaDirectorySettings', 'googleMapsSection'),			// section callback function name, creates content between section title and section options, not used yet
			CitadelaDirectorySettings::$settingsPageId	// settings page id
		);

		add_settings_section(
			'citadela_section_recaptcha',
			esc_html__('Google reCaptcha v3 Keys', 'citadela-directory'),
			array('CitadelaDirectorySettings', 'recaptchaSection'),
			CitadelaDirectorySettings::$settingsPageId
		);
	}

	public static function settings(){

		return array(
			'google_maps_api_key' => array(
				'title' 	=> esc_html__('API Key', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'text',
				'section'	=> 'citadela_section_google_maps',
				'default'	=> '',
			),
			'google_recaptcha_site_key' => array(
				'title' 	=> esc_html__('Site Key', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'text',
				'section'	=> 'citadela_section_recaptcha',
				'default'	=> '',
			),
			'google_recaptcha_secret_key' => array(
				'title' 	=> esc_html__('Secret Key', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'text',
				'section'	=> 'citadela_section_recaptcha',
				'default'	=> '',
			),
		);
	}
}
