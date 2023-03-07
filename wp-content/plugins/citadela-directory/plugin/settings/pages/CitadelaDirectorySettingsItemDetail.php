<?php
/**
 * Citadela Listing Settings - Item Detail page
 *
 */

class CitadelaDirectorySettingsItemDetail extends CitadelaDirectorySettings {

	private static $sectionPrefix = 'citadela_section_item_detail_';
	private static $options;


	public function __construct(){
		throw new LogicException(__CLASS__ . ' is a static class. Can not be instantiate.');
	}


	public static function create(){
		

		self::$options = CitadelaDirectorySettings::$currentOptions;	

		register_setting(
			CitadelaDirectorySettings::$settingsPageId,
			CitadelaDirectorySettings::$settingsKey,
			array(
				'sanitize_callback' => array(__CLASS__, 'sanitize'),
			)
		);

		self::createSections();

		$settings = self::settings();
		if( $settings ){
			CitadelaDirectorySettings::addSettings($settings);
		}
		
	}


	private static function createSections(){

		add_settings_section(
			self::$sectionPrefix.'enable',
            null,
			[__CLASS__, 'enable_section'],
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'slug_settings',
            null,
			[__CLASS__, 'slug_settings_section'],
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'general_settings',
            esc_html__('General settings', 'citadela-directory'),
			[__CLASS__, 'general_settings_section'],
			CitadelaDirectorySettings::$settingsPageId
		);
		
	}

	public static function enable_section(){

		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		?>
		<p>
			<?php esc_html_e('This setting will enable editing of Item Posts using Gutenberg editor. This feature will allow you to have greater administration flexibility. Please be careful which blocks you include in the item detail as everything will be placed in the "Item Content" block on Item Detail Special page.', 'citadela-directory'); ?>
		</p> 
		<?php

		CitadelaDirectorySettings::addSetting( 'enable', [
				'title' 	=> esc_html__('Enable Gutenberg editor', 'citadela-directory'),
				'label' 	=> esc_html__('Use Gutenberg editor for the content of listing items', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> 0,
		] );

        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function slug_settings_section(){

		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		CitadelaDirectorySettings::addSetting( 'item_slug', [
				'title' 	=> esc_html__('Item detail page slug', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'text',
				'section'	=> self::$sectionPrefix.'slug_settings',
				'default'	=> 'item',
		] );

        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function general_settings_section(){

		echo CitadelaDirectorySettings::sectionStart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		CitadelaDirectorySettings::addSetting( 'item_website_rel', [
				'title' 	=> esc_html__('Website link rel attribute', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'select',
				'options'	=> [
					'nofollow' => "nofollow",
					'dofollow' => "dofollow",
				],
				'section'	=> self::$sectionPrefix.'general_settings',
				'default'	=> 'nofollow',
		] );

        echo CitadelaDirectorySettings::sectionEnd(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	
	public static function settings(){
		return [];
	}

	
	public static function sanitize( $data ){
		$data['enable'] = isset($data['enable']) && $data['enable'] !== false ? true : false;
		$data['item_slug'] = isset($data['item_slug']) && $data['item_slug'] !== "" ? sanitize_title( $data['item_slug'] ) : "item";
		return $data;
	}

}
