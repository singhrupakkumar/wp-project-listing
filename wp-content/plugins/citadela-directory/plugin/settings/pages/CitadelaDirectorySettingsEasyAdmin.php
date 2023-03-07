<?php
/**
 * Citadela Listing Settings - Easy Admin page
 *
 */

class CitadelaDirectorySettingsEasyAdmin extends CitadelaDirectorySettings {
	private static $sectionPrefix = 'citadela_section_easyadmin_';

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
			self::$sectionPrefix.'enable',
            null,
			array('CitadelaDirectorySettings', 'easyadminSection'),
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'general',
			esc_html__('General settings', 'citadela-directory'),
			null,
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'header',
			esc_html__('Header settings', 'citadela-directory'),
			null,
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'header_menu',
			esc_html__('Header menu settings', 'citadela-directory'),
			null,
			CitadelaDirectorySettings::$settingsPageId
		);

		add_settings_section(
			self::$sectionPrefix.'metabox',
			esc_html__('Metabox content settings', 'citadela-directory'),
			null,
			CitadelaDirectorySettings::$settingsPageId
		);
	}

	public static function settings(){

		return array(
			'enable' => array(
				'title' 	=> esc_html__('Enable', 'citadela-directory'),
				'label' 	=> esc_html__('Use Easy Admin interface for your subscribers', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'checkbox',
				'section'	=> self::$sectionPrefix.'enable',
				'default'	=> 0,
			),

			/*
			* GENERAL
			*/


			'maxWidth' => array(
				'title' 	=> esc_html__('Maximum width', 'citadela-directory'),
				'desc' 		=> '1000 - 1800px',
				'type' 		=> 'number',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> 1200,
				'params'	=> [
					'min' => 1000,
					'max' => 1800,
				],
				'less'		=> 'maxWidth',
				'less_unit' => 'px',
			),
			'generalBackground' => array(
				'title' 	=> esc_html__('General background', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'background',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> array(
						'color'		=> '#F1F1F1',
						'image'		=> '',
						'repeat'	=> 'no-repeat',
						'position'	=> 'center',
						'scroll'	=> 'scroll',
						'size'		=> 'cover',
					),
				'less'		=> 'generalBackground',
			),
			'generalTitlesColor' => array(
				'title' 	=> esc_html__('Titles color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> '#666666',
				'less'		=> 'generalTitlesColor',
			),
			'generalTextColor' => array(
				'title' 	=> esc_html__('Text color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> '#888888',
				'less'		=> 'generalTextColor',
			),
			'generalLinksColor' => array(
				'title' 	=> esc_html__('Links color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> '#3BA5BC',
				'less'		=> 'generalLinksColor',
			),
			'generalButtonColor' => array(
				'title' 	=> esc_html__('Button color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> '#3BA5BC',
				'less'		=> 'generalButtonColor',
			),
			'generalButtonTextColor' => array(
				'title' 	=> esc_html__('Button text color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'general',
				'default'	=> '#ffffff',
				'less'		=> 'generalButtonTextColor',
			),

			/*
			* HEADER
			*/

			'siteLogo' => array(
				'title' 	=> esc_html__('Site logo', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'image',
				'section'	=> self::$sectionPrefix.'header',
				'default'	=> '',
				'params'	=> [
					'delete' => true,
					'text_input' => false,
				],
			),
			'headerBackground' => array(
				'title' 	=> esc_html__('Background', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'background',
				'section'	=> self::$sectionPrefix.'header',
				'default'	=> array(
						'color'		=> '#2d2d2d',
						'image'		=> '',
						'repeat'	=> 'no-repeat',
						'position'	=> 'center',
						'scroll'	=> 'scroll',
						'size'		=> 'cover',
					),
				'less'		=> 'headerBackground',
			),
			'headerTextColor' => array(
				'title' 	=> esc_html__('Text color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'header',
				'default'	=> '#ffffff',
				'less'		=> 'headerTextColor',
			),

			/*
			* HEADER MENU
			*/
			'headerMenuBackground' => array(
				'title' 	=> esc_html__('Background', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'background',
				'section'	=> self::$sectionPrefix.'header_menu',
				'default'	=> array(
						'color'		=> '#383838',
						'image'		=> '',
						'repeat'	=> 'no-repeat',
						'position'	=> 'center',
						'scroll'	=> 'scroll',
						'size'		=> 'cover',
					),
				'less'		=> 'headerMenuBackground',
			),
			'headerMenuTextColor' => array(
				'title' 	=> esc_html__('Text color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'header_menu',
				'default'	=> '#ffffff',
				'less'		=> 'headerMenuTextColor',
			),

			/*
			* METABOX
			*/

			'contentMetaboxBackground' => array(
				'title' 	=> esc_html__('Background', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'background',
				'section'	=> self::$sectionPrefix.'metabox',
				'default'	=> array(
						'color'		=> '#ffffff',
						'image'		=> '',
						'repeat'	=> 'no-repeat',
						'position'	=> 'center',
						'scroll'	=> 'scroll',
						'size'		=> 'cover',
					),
				'less'		=> 'contentMetaboxBackground',
			),
			'contentMetaboxLabelColor' => array(
				'title' 	=> esc_html__('Label color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'metabox',
				'default'	=> '#666666',
				'less'		=> 'contentMetaboxLabelColor',
			),
			'contentMetaboxTextColor' => array(
				'title' 	=> esc_html__('Text color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'metabox',
				'default'	=> '#888888',
				'less'		=> 'contentMetaboxTextColor',
			),
			'contentMetaboxBorderColor' => array(
				'title' 	=> esc_html__('Border color', 'citadela-directory'),
				'desc' 		=> '',
				'type' 		=> 'colorpicker',
				'section'	=> self::$sectionPrefix.'metabox',
				'default'	=> '#e8e8e8',
				'less'		=> 'contentMetaboxBorderColor',
			),
		);
	}


}
