<?php
/**
 * Citadela Listing Settings - Claim Listing page
 *
 */

class CitadelaDirectorySettingsClaimListing extends CitadelaDirectorySettings {
	private static $sectionPrefix = 'citadela_section_claim_listing_';
	private static $options;

	public function __construct()
	{
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function create()
	{
		self::createSections();
		$settings = self::settings();
		CitadelaDirectorySettings::addSettings($settings);
	}

	private static function createSections()
	{
		add_settings_section(
			self::$sectionPrefix.'enable',
			null,
			array('CitadelaDirectorySettings', 'claimListingSection'),
			CitadelaDirectorySettings::$settingsPageId
		);
		add_settings_section(
			self::$sectionPrefix.'email',
			esc_html__('Email', 'citadela-directory'),
			null,
			CitadelaDirectorySettings::$settingsPageId
		);
	}

	public static function settings()
	{
		return [
			'enable' => [
				'title' 	=> esc_html__('Enable', 'citadela-directory'),
				'label' 	=> esc_html__('Turn on Claim Listing feature', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> 0,
			],
			'email_subject' => [
				'title' 	=> esc_html__('Subject', 'citadela-directory'),
				'desc' 		=> esc_html__('Subject for the email notification', 'citadela-directory'),
				'type' 		=> 'text',
				'section'	=> self::$sectionPrefix.'email',
				'class'		=> 'regular-text',
				'default'	=> esc_html__('Claim Listing Request', 'citadela-directory')
			],
			'email_message' => [
				'title' 	=> esc_html__('Message', 'citadela-directory'),
				'desc' 		=> esc_html__('Message for the email notification', 'citadela-directory'),
				'type' 		=> 'textarea',
				'section'	=> self::$sectionPrefix.'email',
				'class'		=> 'regular-text',
				'default'	=> wp_kses_post('User: {user} <br> Item: {item} <br><br> {actions}', 'citadela-directory')
			],
		];
	}
}
