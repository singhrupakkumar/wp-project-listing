<?php

namespace Citadela\Pro;


class Customize {

	static function register($wp_customize) {
		self::register_controls();
		self::panels( $wp_customize );
		self::sections( $wp_customize );
		self::controls( $wp_customize );
	}



	protected static function register_controls() {
		foreach ( [
			'Notification', 
			'SectionTitle'
		] as $control_name) {
			require_once __DIR__ . "/Customizer_Controls/{$control_name}.php";
		}
	}



	protected static function panels( $wp_customize ) {
		foreach ( self::config( 'panels' ) as $key => $values ) {
			$wp_customize->add_panel( "citadela_panel_$key", [
				'title'          => $values['title'],
				'description'    => $values['description'],
				'priority'       => $values['priority'],
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
			] );
		}
	}



	protected static function sections( $wp_customize ) {
		foreach ( self::config( 'sections' ) as $key => $values ) {
			$wp_customize->add_section( "citadela_section_$key", [
				'title'          => $values['title'],
				'description'    => $values['description'],
				'priority'       => $values['priority'],
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'panel'          => $values['panel'] ? "citadela_panel_{$values['panel']}" : '',
			] );
		}
	}



	protected static function controls( $wp_customize ) {

		// add general notification messages outside custom panels

		$wp_customize->add_setting(
			"citadela_setting_notification_custom_logo",
			[]
		);
		$wp_customize->add_control(
			new \NotificationControl(
				$wp_customize,
				"citadela_setting_notification_custom_logo",
				array(
					'type'          => 'notification',
					'args'			=> [
							'message' => __("Custom Header with Custom Logo is enabled for this page. Change of logo image may not be visible in preview.", 'citadela-pro'),
					],
					'section'       => 'title_tagline',
					'settings'      => "citadela_setting_notification_custom_logo",
					'priority'		=> 0,
					'active_callback' => [ __CLASS__, 'custom_logo_callback' ]
				)
			)
		);


		foreach ( self::config('controls') as $section_name => $controls ) {
			
			//check if it's default WordPress section or custom Citadela section
			$section = in_array($section_name, [ 'title_tagline' ] ) ? $section_name : 'citadela_section_'.$section_name;

			$sectionData = self::config("sections.{$section_name}");
			
			if( is_array($sectionData) && isset( $sectionData['notifications'] ) ){
				foreach ($sectionData['notifications'] as $notification) {
					
					if( $notification == 'custom_header') {
							$wp_customize->add_setting(
								"citadela_setting_notification",
								[]
							);
							$wp_customize->add_control(
								new \NotificationControl(
									$wp_customize,
									"citadela_setting_notification",
									array(
										'type'          => 'notification',
										'args'			=> [
											'message' => __("Custom Header is enabled. Following options may not have effect for this page.", 'citadela-pro'),
										],
										'section'       => $section,
										'settings'      => "citadela_setting_notification",
										'priority'		=> 0,
										'active_callback' => [ __CLASS__, 'custom_header_callback' ]
									)
								)
							);
					}
				}
			}
			

			foreach ( $controls as $key => $data ) {

				//divide real controls with settings and simple information parts like section title
				if ( $data['control_type'] == 'section_title' ){
					$wp_customize->add_setting(
						"citadela_setting_{$key}",
						[]
					);
					$wp_customize->add_control(
						new \SectionTitleControl(
							$wp_customize,
							"citadela_setting_{$key}",
							array(
								'type'          => $key,
								'args'			=> $data,
								'section'       => $section,
								'settings'      => "citadela_setting_{$key}",
								'priority'		=> isset( $data['priority'] ) ? $data['priority'] : 0,
							)
						)
					);

				}else{


					$settingsData = [];
					$settingsData['type'] = 'theme_mod';
					$settingsData['default'] = $data['default'];
					$settingsData['transport'] = 'refresh';
					if( isset( $data['sanitize_callback'] ) ){
						$settingsData['sanitize_callback'] = $data['sanitize_callback'];
					}
					if( isset( $data['validate_callback'] ) ){
						$settingsData['validate_callback'] = $data['validate_callback'];
					}

					$wp_customize->add_setting(
						"citadela_setting_$key",
						$settingsData
					);

					$description = isset( $data['description'] ) ? $data['description'] : '';
					$priority = isset( $data['priority'] ) ? $data['priority'] : '';

					switch ($data['control_type']) {
						case 'number':
							$wp_customize->add_control(
								new \WP_Customize_Control(
									$wp_customize,
									"citadela_setting_$key",
									array(
										'type'          => 'number',
										'label'         => $data['title'],
										'description'   => $description,
										'section'       => $section,
										'settings'      => "citadela_setting_$key",
										'priority'      => $priority,
										'input_attrs' 	=> $data['input_attrs']
									)
								)
							);
							break;

						case 'color':
							$wp_customize->add_control(
								new \WP_Customize_Color_Control(
									$wp_customize,
									"citadela_setting_$key",
									array(
										'label'         => $data['title'],
										'description'   => $description,
										'section'       => $section,
										'settings'      => "citadela_setting_$key",
										'priority'      => $priority,
									)
								)
							);
							break;

						case 'select':
							$wp_customize->add_control(
								new \WP_Customize_Control (
									$wp_customize,
									"citadela_setting_$key",
									array(
										'type'              => 'select',
										'label'             => $data['title'],
										'description'       => $description,
										'choices'           => $data['choices'],
										'section'           => $section,
										'settings'          => "citadela_setting_$key",
										'priority'          => $priority,
										'active_callback'   => $data['active_callback'],
									)
								)
							);
						break;

						case 'google_font_select':
							$wp_customize->add_control(
								new \WP_Customize_Control (
									$wp_customize,
									"citadela_setting_$key",
									array(
										'type'              => 'select',
										'label'             => $data['title'],
										'description'       => $description,
										'choices'           => Google_Fonts::for_selectbox(),
										'section'           => $section,
										'settings'          => "citadela_setting_$key",
										'priority'          => $priority,
										'active_callback'   => $data['active_callback'],
									)
								)
							);
						break;



						case 'checkbox':
							$wp_customize->add_control(
								new \WP_Customize_Control (
									$wp_customize,
									"citadela_setting_$key",
									array(
										'type'              => 'checkbox',
										'label'             => $data['title'],
										'description'       => $description,
										'section'           => $section,
										'settings'          => "citadela_setting_$key",
										'priority'          => $priority,
										'active_callback'   => $data['active_callback'],
									)
								)
							);
						break;

						case 'image':
							$wp_customize->add_control(
								new \WP_Customize_Image_Control(
									$wp_customize,
									"citadela_setting_$key",
									array(
										'label'             => $data['title'],
										'description'       => $description,
										'section'           => $section,
										'settings'          => "citadela_setting_$key",
										'priority'          => $priority,
									)
								)
							);
						break;

						case 'range':
							$wp_customize->add_control(
								new \WP_Customize_Control(
									$wp_customize,
									"citadela_setting_$key",
									array(
										'type' 				=> 'range',
										'label'             => $data['title'],
										'description'       => $description,
										'section'           => $section,
										'settings'          => "citadela_setting_$key",
										'priority'          => $priority,
										'input_attrs' 		=> $data['input_attrs']
									)
								)
							);
						break;

						default: break;
					}
				}
			}
		}
	}


	static function config( $key = null ) {
		return \ctdl\pro\dot_get( [

			'panels' => [
				'appearance' => [
					'title'         => __( 'Appearance', 'citadela-pro' ),
					'description'   => __( 'Appearance settings for your website', 'citadela-pro' ),
					'priority'      => 22,
				],
			],
			'sections' => [
				'general_layout'    => [
					'title'         => __( 'General Layout', 'citadela-pro' ),
					'description'   => __( 'Select layout for your website', 'citadela-pro' ),
					'priority'      => 21,
					'panel'         => '',
				],
				'announcements_bar'	=> [
					'title' 		=> __( 'Announcements Bar', 'citadela-pro' ),
					'description'	=> '',
					'priority'		=> 23,
					'panel'			=> '',
				],
				'header_layout_section' => [
					'title'         => __( 'Header Layout', 'citadela-pro' ),
					'description'   => __( 'Settings for site header', 'citadela-pro' ),
					'priority'      => '',
					'panel'         => 'appearance',
				],				

				'main_menu' => [
					'title'         => __( 'Main Menu', 'citadela-pro' ),
					'description'   => __( 'Settings for main menu in header', 'citadela-pro' ),
					'priority'      => '',
					'panel'         => 'appearance',
				],
				'header_background' => [
					'title'         => __( 'Header Background', 'citadela-pro' ),
					'description'   => __( 'Background settings for your website header', 'citadela-pro' ),
					'priority'      => '',
					'panel'         => 'appearance',
					'notifications'	=> [
						'custom_header',
					],
				],
				'colors'            => [
					'title'         => __( 'Colors', 'citadela-pro' ),
					'description'   => __( 'Select color combinations for your website', 'citadela-pro' ),
					'priority'      => '',
					'panel'         => 'appearance',
				],
				'typography'        => [
					'title'         => __( 'Typography', 'citadela-pro' ),
					'description'   => __( 'Select Google Font for your website', 'citadela-pro' ),
					'priority'      => '',
					'panel'         => 'appearance',
				],
			],
			'controls' => [

				'colors' => [
					'decorationColor'   => [
						'control_type'      => 'color',
						'title'             => __( 'Decoration Color', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#016dff',
						'theme_default'     => '#016dff',
						'less_var'          => 'decorationColor',
					],
					'pageBgColor'       =>  [
						'control_type'      => 'color',
						'title'             => __( 'Page Color', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#ffffff',
						'theme_default'     => '#ffffff',
						'less_var'          => 'pageBgColor',
					],
					'footerBgColor'     =>  [
						'control_type'      => 'color',
						'title'             => __( 'Footer Color', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#f5f5f5',
						'theme_default'     => '#f5f5f5',
						'less_var'          => 'footerBgColor',
					],
				],
				'announcements_bar' => [
					'announcementsBarShowInCustomizer' =>	[
						'control_type'      => 'checkbox',
						'title'             => __( 'Show preview of Announcement Bar', 'citadela-pro' ),
						'description'       => __( 'Preview is displayed only in Customizer screen.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 0,
					],
					'announcementsBarBgColor' =>	[
						'control_type'      => 'color',
						'title'             => __( 'Bar Background color', 'citadela-pro' ),
						'description'       => __( 'Applied for Simple type of Announcements bar.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#e6a713',
						'theme_default'     => '#e6a713',
						'less_var'          => 'announcementsBarBgColor',
					],
					'announcementsBarButtonBgColor' =>	[
						'control_type'      => 'color',
						'title'             => __( 'Button Background color', 'citadela-pro' ),
						'description'       => __( 'Applied for Simple type of Announcements bar.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#477fe0',
						'theme_default'     => '#477fe0',
						'less_var'          => 'announcementsBarButtonBgColor',
					],
				],
				'typography'        => [
					'textFont'          => [
						'control_type'      => 'google_font_select',
						'title'             => __( 'Text Font', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'Open Sans',
						'theme_default'     => 'Open Sans',
						'less_var'          => 'textFont',
					],
					'titlesFont'        => [
						'control_type'      => 'google_font_select',
						'title'             => __( 'Titles Font', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'Open Sans',
						'theme_default'     => 'Open Sans',
						'less_var'          => 'titlesFont',
					],
					'titlesFontWeight'  => [
						'control_type'      => 'select',
						'title'             => __( 'Titles Style', 'citadela-pro' ),
						'description'       => __( 'Availability of selected style depends on selected font for titles.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 400,
						'theme_default'     => 400,
						'choices'           => array(
							'100'  => __( 'Light (100)', 'citadela-pro' ),
							'400'  => __( 'Normal (400)', 'citadela-pro' ),
							'700'  => __( 'Bold (700)', 'citadela-pro' ),
							'900'  => __( 'Extra Bold (900)', 'citadela-pro' ),
						),
						'less_var'          => 'titlesFontWeight', 
					],
				],
				



				'general_layout'    => [
					'themeLayout'   => [
						'control_type'      => 'select',
						'title'             => __( 'Theme Layout', 'citadela-pro' ),
						'description'       => __( 'Select layout for your website', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'classic',
						'choices'           => Config::get( 'theme_layouts' ),
					],
					
					

					
					'widgets_settings_title' => [
						'control_type'		=> 'section_title',
						'title' 			=> __( 'Collapsible footer widgets options', 'citadela-pro' ),
						'description' 		=> '',
					],
					'footerCollapsibleWidgetsApply' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Enable collapsible footer wigets area', 'citadela-pro' ),
						'description'       => __( 'Widgets area in footer can be opened or closed on mobile.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => true,
					],
					'footerCollapsibleWidgetsOpened' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Opened footer wigets area', 'citadela-pro' ),
						'description'       => __( 'Sets if widgets in footer are opened or closed by default on mobile after page load. Applicable if collapsible widgets are enabled.', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],

					// functionality for widget titles deprecated from WP 5.8
					/*
					'leftCollapsibleWidgetsOpened' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Opened widgets in left sidebar area', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
					'rightCollapsibleWidgetsOpened' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Opened widgets in right sidebar area', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
					*/
					'content_width_sizes_title' => [
						'control_type'		=> 'section_title',
						'title' 			=> __( 'Content width sizes', 'citadela-pro' ),
						'description' 		=> __( 'Sizes for Content, Wide and Fullwidth layout types used in design.', 'citadela-pro' ),
					],

					'contentSizeWidth'   => [
						'control_type'      => 'number',
						'title'             => __( 'Content size', 'citadela-pro' ),
						'description'       => '768 - 960px',
						'priority'          => '',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 768,
						'input_attrs'		=> array(
												'min' => 768,
												'max' => 960,
												'step' => 1,
												),
						'less_var'          => 'contentSizeWidth',
					],

					'wideSizeWidth'   => [
						'control_type'      => 'number',
						'title'             => __( 'Wide size', 'citadela-pro' ),
						'description'       => '1200 - 1400px',
						'priority'          => '',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 1200,
						'input_attrs'		=> array(
												'min' => 1200,
												'max' => 1400,
												'step' => 1,
												),
						'less_var'          => 'wideSizeWidth',
					],

					'fullSizeWidth'   => [
						'control_type'      => 'number',
						'title'             => __( 'Full size', 'citadela-pro' ),
						'description'       => __( 'Minimum value 1500px, leave empty for unlimited width.', 'citadela-pro' ),
						'priority'          => '',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => '',
						'input_attrs'		=> array(
												'min' => 1500,
												'step' => 1,
												),
						'allow_empty' 		=> true,
						'less_var'          => 'fullSizeWidth',
					],

					'borders_title' => [
						'control_type'			=> 'section_title',
						'title' 		=> __( 'Border radius options', 'citadela-pro' ),
						'description' 	=> __( 'Define border radius for different parts of design.', 'citadela-pro' ),
					],

					'generalBorderRadius'   => [
						'control_type'      => 'number',
						'title'             => __( 'General Border Radius', 'citadela-pro' ),
						'description'       => '0 - 35px',
						'priority'          => '',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 0,
						'input_attrs'		=> array(
												'min' => 0,
												'max' => 35,
												'step' => 1,
												),
						'less_var'          => 'generalBorderRadius',
					],
					'buttonBorderRadius'   => [
						'control_type'      => 'number',
						'title'             => __( 'Buttons Border Radius', 'citadela-pro' ),
						'description'       => '0 - 50px',
						'priority'          => '',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 40,
						'input_attrs'		=> array(
												'min' => 0,
												'max' => 50,
												'step' => 1,
												),
						'less_var'          => 'buttonBorderRadius',
					],
				],

				'header_layout_section'    => [
					'headerLayout'  =>  [
						'control_type'      => 'select',
						'title'             => __( 'Header Layout', 'citadela-pro' ),
						'description'       => __( 'Select layout for website header', 'citadela-pro' ),
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'classic',
						'choices'           => Config::get( 'header_layouts' ),
					],
					'headerFullwidth' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Fullwidth header', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
					'menuButtonColor'     =>  [
						'control_type'      => 'color',
						'title'             => __( 'Menu button color', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '',
						'theme_default'     => '#016dff',
						'less_var'          => 'menuButtonColor',
					],



					'desktop_settings' => [
						'control_type'		=> 'section_title',
						'title' 			=> __( 'Desktop settings', 'citadela-pro' ),
						'description' 		=> '',
					],
					'logoImageMaxWidthDesktop'   => [
						'control_type'      => 'number',
						'title'             => __( 'Logo image max width', 'citadela-pro' ),
						'description'       => '30 - 400px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 150,
						'input_attrs'		=> array(
												'min' => 30,
												'max' => 400,
												'step' => 1,
											),
					],
					'logoSpaceMaxWidthDesktop'   => [
						'control_type'      => 'number',
						'title'             => __( 'Logo space max width', 'citadela-pro' ),
						'description'       => '200 - 600px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 400,
						'input_attrs'		=> array(
												'min' => 200,
												'max' => 600,
												'step' => 1,
											),
					],
					'spaceAroundLogoDesktop'   => [
						'control_type'      => 'number',
						'title'             => __( 'Space around logo', 'citadela-pro' ),
						'description'       => '10 - 60px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => '',
						'input_attrs'		=> array(
												'min' => 10,
												'max' => 60,
												'step' => 1,
											),
						'allow_empty' 		=> true,
						'less_var'          => 'spaceAroundLogoDesktop',
					],
					'hideSitetitleAndTaglineDesktop'  => [
						'control_type'      => 'select',
						'title'             => __( 'Hide site title and tagline', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'none',
						'theme_default'     => 'none',
						'choices'           => array(
							'none'  => __( 'None', 'citadela-pro' ),
							'hide-title-and-tagline'  => __( 'Hide site title and tagline', 'citadela-pro' ),
							'hide-tagline'  => __( 'Hide tagline only', 'citadela-pro' ),
						),
					],
					'collapseMenuToButton' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Collapse menu to button', 'citadela-pro' ),
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
					'responsiveMenuButtonSpace'   => [
						'control_type'      => 'number',
						'title'             => __( 'Space around menu button', 'citadela-pro' ),
						'description'       => '0 - 40px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => '',
						'input_attrs'		=> array(
												'min' => 0,
												'max' => 40,
												'step' => 1,
											),
						'allow_empty' 		=> true,
						'less_var'          => 'responsiveMenuButtonSpace',
					],





					'mobile_settings' => [
						'control_type'		=> 'section_title',
						'title' 			=> __( 'Mobile settings', 'citadela-pro' ),
						'description' 		=> '',
					],
					'logoImageMaxWidthMobile'   => [
						'control_type'      => 'number',
						'title'             => __( 'Logo image max width', 'citadela-pro' ),
						'description'       => '30 - 400px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => 150,
						'input_attrs'		=> array(
												'min' => 30,
												'max' => 400,
												'step' => 1,
											),
					],
					'logoFontSize'   => [
						'control_type'      => 'number',
						'title'             => __( 'Logo font size', 'citadela-pro' ),
						'description'       => __( 'In "em" unit', 'citadela-pro' ),
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => '',
						'input_attrs'		=> array(
												'min' => 0.1,
												//'max' => 200,
												'step' => 0.1,
											),
						'allow_empty' => true,
					],
					'spaceAroundLogoMobile'   => [
						'control_type'      => 'number',
						'title'             => __( 'Space around logo', 'citadela-pro' ),
						'description'       => '10 - 60px',
						'validate_callback' => array( __CLASS__, 'validate_number_range' ),
						'active_callback'   => '',
						'default'           => '',
						'input_attrs'		=> array(
												'min' => 10,
												'max' => 60,
												'step' => 1,
											),
						'allow_empty' 		=> true,
						'less_var'          => 'spaceAroundLogoMobile',
					],
					'hideSitetitleAndTaglineMobile'  => [
						'control_type'      => 'select',
						'title'             => __( 'Hide site title and tagline', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'none',
						'theme_default'     => 'none',
						'choices'           => array(
							'none'  => __( 'None', 'citadela-pro' ),
							'hide-title-and-tagline'  => __( 'Hide site title and tagline', 'citadela-pro' ),
							'hide-tagline'  => __( 'Hide tagline only', 'citadela-pro' ),
						),
					],
					'logoAlignLeftMobile' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Left align logo', 'citadela-pro' ),
						'description'       => __( 'Align logo section to the left side', 'citadela-pro' ),
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],




					'sticky_header_settings' => [
						'control_type'		=> 'section_title',
						'title' 			=> __( 'Sticky header settings', 'citadela-pro' ),
						'description' 		=> '',
					],
					'stickyHeaderDesktop'  => [
						'control_type'      => 'select',
						'title'             => __( 'Sticky header on desktop', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'sticky-header-desktop-none',
						'choices'           => array(
							'sticky-header-desktop-none'  	=> __( 'None', 'citadela-pro' ),
							'sticky-header-desktop-burger'  => __( 'Burger menu only', 'citadela-pro' ),
							'sticky-header-desktop-full'  	=> __( 'Full Sticky header', 'citadela-pro' ),
						),
					],
					'hideSitetitleOnFullStickyDesktop' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Hide Site title', 'citadela-pro' ),
						'description'       => __( 'Hidden title in full sticky header on desktop', 'citadela-pro' ),
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],

					'stickyHeaderMobile'  => [
						'control_type'      => 'select',
						'title'             => __( 'Sticky header on mobile', 'citadela-pro' ),
						'description'       => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'sticky-header-mobile-burger',
						'choices'           => array(
							'sticky-header-mobile-none'  	=> __( 'None', 'citadela-pro' ),
							'sticky-header-mobile-burger'  	=> __( 'Burger menu only', 'citadela-pro' ),
							'sticky-header-mobile-full'  	=> __( 'Full Sticky header', 'citadela-pro' ),
						),
					],
					'hideSitetitleOnFullStickyMobile' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Hide site title', 'citadela-pro' ),
						'description'       => __( 'Hidden title in full sticky header on mobile', 'citadela-pro' ),
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
				],








				'header_background' => [
					'headerBgColor'     =>  [
						'control_type'      => 'color',
						'title'             => __( 'Header Color', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => 'sanitize_hex_color',
						'default'           => '#ffffff',
						'theme_default'     => '#ffffff',
						'less_var'          => 'headerBgColor',
					],
					'headerColorOverlay' => [
						'control_type'      => 'checkbox',
						'title'             => __( 'Header Color Overlay', 'citadela-pro' ),
						'description'       => __( 'Set header color as background image overlay', 'citadela-pro' ),
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
					],
					'headerImageUrl'    => [
						'control_type'      => 'image',
						'title'             => __( 'Header Image', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'default'           => '',
						'less_var'          => 'headerImageUrl',
					],
					'headerImageRepeat' =>  [
						'control_type'      => 'select',
						'title'             => __( 'Header Image Repeat', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'repeat',
						'choices'           => [
							'no-repeat'  => __( 'No Repeat', 'citadela-pro' ),
							'repeat'     => __( 'Tile', 'citadela-pro' ),
							'repeat-x'   => __( 'Tile Horizontally', 'citadela-pro' ),
							'repeat-y'   => __( 'Tile Vertically', 'citadela-pro' ),
						],
						'less_var'          => 'headerImageRepeat',
					],
					'headerImageSize' =>    [
						'control_type'      => 'select',
						'title'             => __( 'Header Image Size', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'cover',
						'choices'           => [
							'initial'       => __( 'Original', 'citadela-pro' ),
							'cover'         => __( 'Cover', 'citadela-pro' ),
							'contain'       => __( 'Contain', 'citadela-pro' ),
						],
						'less_var'          => 'headerImageSize',
					],
					'headerImagePosition' =>    [
						'control_type'      => 'select',
						'title'             => __( 'Header Image Position', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => 'center',
						'choices'           => [
							'top left'      => __( 'Top Left', 'citadela-pro' ),
							'top'           => __( 'Top', 'citadela-pro' ),
							'top right'     => __( 'Top Right', 'citadela-pro' ),
							'left'          => __( 'Left', 'citadela-pro' ),
							'center'        => __( 'Center', 'citadela-pro' ),
							'right'         => __( 'Right', 'citadela-pro' ),
							'bottom left'   => __( 'Bottom Left', 'citadela-pro' ),
							'bottom'        => __( 'Bottom', 'citadela-pro' ),
							'bottom right'  => __( 'Bottom Right', 'citadela-pro' ),
						],
						'less_var'          => 'headerImagePosition',
					],
					'headerImageAttachment' =>  [
						'control_type'      => 'checkbox',
						'title'             => __( 'Fixed background image', 'citadela-pro' ),
						'description'       => '',
						'priority'          => '',
						'sanitize_callback' => '',
						'active_callback'   => '',
						'default'           => false,
						'values'            => [
							'saved' => 'fixed', //value which we need to use in less when option is checked (saved true in databae)
							'default' => 'scroll', //value which we need to use in less when option not checked
						],
						'less_var'          => 'headerImageAttachment',
					],
				],
			],

		], $key );
	}


	static function validate_number_range( $validity, $value, $setting ) {

		$settingData = $setting->id_data();
		$settingId = str_replace( "citadela_setting_", "", $settingData['base'] );

		//$config = self::config("controls.general_layout.{$settingId}");
		$config = self::get_setting_data( $settingId );
		$default = isset( $config['default'] ) ? $config['default'] : '';
		$allow_empty = isset( $config['allow_empty'] ) ? $config['allow_empty'] : false;
		$number_value = $config['input_attrs']['step'] == 0.1 ? floatval( $value ) : intval( $value );
		
		if( $value != '' && $config['input_attrs']['step'] == 1 && strpos( $value, '.' ) ){
			$validity->add( 'invalid', __( "Do not use float numbers, please.", 'citadela-pro' ) );
			return $validity;
		}

		if( $allow_empty ){
			
			if ( $value != '' && isset( $config['input_attrs']['max'] ) && $config['input_attrs']['max'] < $number_value ) {
				// translators: 1. maximum number value
				$validity->add( 'invalid',  sprintf( __( "Insert number smaller than %1\$s, or leave empty for default value.", 'citadela-pro' ), $config['input_attrs']['max'] ) );
			
			}elseif ( $value != '' && isset( $config['input_attrs']['min'] ) && $config['input_attrs']['min'] > $number_value ) {
				// translators: 1. minimum number value
				$validity->add( 'invalid',  sprintf( __( "Insert number larger than %1\$s, or leave empty for default value.", 'citadela-pro' ), $config['input_attrs']['min'] ) );
			}

		}else{
			
			if ( isset( $config['input_attrs']['max'] ) && $config['input_attrs']['max'] < $number_value ) {
				// translators: 1. maximum number value
				$validity->add( 'invalid',  sprintf( __( "Insert number smaller than %1\$s.", 'citadela-pro' ), $config['input_attrs']['max'] ) );
			
			}elseif ( isset( $config['input_attrs']['min'] ) && $config['input_attrs']['min'] > $number_value ) {
				// translators: 1. minimum number value
				$validity->add( 'invalid',  sprintf( __( "Insert number larger than %1\$s.", 'citadela-pro' ), $config['input_attrs']['min'] ) );
			
			}elseif( $value == '' ){
				$validity->add( 'invalid', __( "Insert valid number, please.", 'citadela-pro' ) );
			}

		}
		
		return $validity;
	}

	static function custom_header_callback() {
		$post_id = \Citadela_Theme::get_instance()->get_page_id();
		return get_post_meta( $post_id, '_citadela_header', true) ? true : false;
	}

	static function custom_logo_callback() {
		$theme_instance = \Citadela_Theme::get_instance();
		$custom_header = $theme_instance->get_page_meta( '_citadela_header' );
		$logo_image = $theme_instance->get_page_meta( '_citadela_header_logo' );

		return $custom_header && $logo_image;
	}

	static function get_setting_data( $setting_id ){
		foreach (self::config("controls") as $section => $controls) {
			foreach ($controls as $key => $data) {
				if( $key === $setting_id ){
					return $data;
				}
			}
		}
	}
}
