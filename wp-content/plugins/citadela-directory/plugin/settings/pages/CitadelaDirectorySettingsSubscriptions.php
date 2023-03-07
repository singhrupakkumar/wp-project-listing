<?php
/**
 * Citadela Listing Settings - General page
 *
 */

class CitadelaDirectorySettingsSubscriptions extends CitadelaDirectorySettings {

	public function __construct()
	{
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}

	public static function create()
	{
		self::createSections();
		
		if( CitadelaDirectory::getInstance()->Subscriptions_instance->enabled_subscription_plugin ){
			return;
		}

		$settings = self::settings();
		CitadelaDirectorySettings::addSettings($settings);
	}

	private static function createSections()
	{
		add_settings_section(
			'citadela_section_subscriptions',
            null,
			['CitadelaDirectorySettings', 'subscriptionsSection'],
			CitadelaDirectorySettings::$settingsPageId
		);
	}

	public static function settings()
	{
		return [
			'enable_subscriptions' => [
				'title' 	=> __('Enable', 'citadela-directory'),
				'label' 	=> __('Enable bundled subscriptions', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> 'citadela_section_subscriptions',
				'default'	=> 0,
			]
		];
	}

}
