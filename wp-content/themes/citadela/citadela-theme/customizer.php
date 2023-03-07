<?php
/**
 * Citadela Theme Customizer
 *
 */

function citadela_theme_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'custom_logo' )->transport  	= 'refresh';


	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'citadela_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'citadela_customize_partial_blogdescription',
		) );
	}

	
	/*
	*	Footer copyright text
	*/

	$wp_customize->add_setting(
		'citadela_setting_footerText',
		array(
			'type'				=> 'theme_mod',
			'sanitize_callback' => 'citadela_sanitize_footer_text',
			'default'           => esc_html__( 'Created with Citadela WordPress Theme by AitThemes', 'citadela' ),
		)
	);

	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'citadela_setting_footerText',
		array(
			'type' => 'textarea',
			'label' => esc_html__( 'Footer Text', 'citadela' ),
			'priority' => 30,
			'section' => 'title_tagline',
			'settings' => 'citadela_setting_footerText'
			)
		)
    );

    if (!defined('CITADELA_PRO_PLUGIN')) {

    	$wp_customize->add_panel( "citadela_panel_appearance", [
				'title'         	=> esc_html__( 'Appearance', 'citadela' ),
				'description'   	=> esc_html__( 'Appearance settings for your website', 'citadela' ),
				'priority'      	=> 22,
				'capability'     	=> 'edit_theme_options',
				'theme_supports' 	=> '',
			] );

    	$wp_customize->add_section( "citadela_section_header_layout_section", [
				'title'          => esc_html__( 'Header Layout', 'citadela' ),
				'description'    => esc_html__( 'Settings for site header', 'citadela' ),
				'priority'       => '',
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'panel'          => 'citadela_panel_appearance',
			] );

    	$wp_customize->add_setting(
			'citadela_setting_hideSitetitleAndTaglineDesktop',
			array(
				'type'				=> 'theme_mod',
				'default'           => 'none',
				'sanitize_callback' => 'citadela_sanitize_text',
			)
		);
    	$wp_customize->add_control(
			new \WP_Customize_Control (
				$wp_customize,
				"citadela_setting_hideSitetitleAndTaglineDesktop",
				array(
					'type'              => 'select',
					'label'             => esc_html__( 'Hide site title and tagline on desktop', 'citadela' ),
					'description'       => '',
					'choices'           => array(
							'none'  => esc_html__( 'None', 'citadela' ),
							'hide-title-and-tagline'  => esc_html__( 'Hide site title and tagline', 'citadela' ),
							'hide-tagline'  => esc_html__( 'Hide tagline only', 'citadela' ),
						),
					'section'           => 'citadela_section_header_layout_section',
					'settings'          => 'citadela_setting_hideSitetitleAndTaglineDesktop',
				)
			)
		);

    	$wp_customize->add_setting(
			'citadela_setting_hideSitetitleAndTaglineMobile',
			array(
				'type'				=> 'theme_mod',
				'default'           => 'none',
				'sanitize_callback' => 'citadela_sanitize_text',
			)
		);
		$wp_customize->add_control(
			new \WP_Customize_Control (
				$wp_customize,
				"citadela_setting_hideSitetitleAndTaglineMobile",
				array(
					'type'              => 'select',
					'label'             => esc_html__( 'Hide site title and tagline on mobile', 'citadela' ),
					'description'       => '',
					'choices'           => array(
							'none'  => esc_html__( 'None', 'citadela' ),
							'hide-title-and-tagline'  => esc_html__( 'Hide site title and tagline', 'citadela' ),
							'hide-tagline'  => esc_html__( 'Hide tagline only', 'citadela' ),
						),
					'section'           => 'citadela_section_header_layout_section',
					'settings'          => 'citadela_setting_hideSitetitleAndTaglineMobile',
				)
			)
		);

        add_action( 'customize_controls_enqueue_scripts', function() {
            wp_enqueue_style( 'citadela-customizer-style',
                citadela_paths()->url->css . '/admin/customizer-style.css',
                [],
                filemtime( citadela_paths()->dir->css . '/admin/customizer-style.css' )
            );

        } );

        $wp_customize->add_setting( 'citadela_upsell_control', ['sanitize_callback' => 'sanitize_text_field'] );
        $wp_customize->add_control(
            new \Citadela\Customizer\Controls\CitadelaUpsellControl( $wp_customize,
                'citadela_upsell_control',
                [
                    'section' => 'citadela_upsells_section',
                    'cta_url' => esc_url(admin_url('themes.php?page=citadela-settings')),
                    'listing_image' => esc_url(citadela_paths()->url->settings . '/templates/img/ctdl-listing-opt.jpg'),
                    'business_image' => esc_url(citadela_paths()->url->settings . '/templates/img/ctdl-business-opt.jpg'),

                ]
            )
        );

        $wp_customize->add_section( 'citadela_upsells_section', [
            'priority' => 10,
            'title'    => esc_html__( 'Pro Features', 'citadela' ),
        ]);

        $wp_customize->register_control_type( '\Citadela\Customizer\Controls\CitadelaUpsellControl' );
    }
}
add_action( 'customize_register', 'citadela_theme_customize_register' );

function citadela_customize_partial_blogname() {
	bloginfo( 'name' );
}

function citadela_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

function citadelaSanitizeCheckbox( $checked, $setting ){
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

function citadela_sanitize_text( $text, $setting ){
	return sanitize_text_field( $text );
}

function citadela_sanitize_footer_text ( $text, $setting ){
	$allowed_html = array(
		'p' => array(),
		'a' => array(
				'href' => array(),
        		'title' => array(),
        		'target' => array(),
        		'follow' => array()
        	),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
		'i' => array(),
	);
	return wp_kses($text, $allowed_html);
}

function citadela_theme_customize_preview_js() {
	wp_enqueue_script( 'citadela-theme-customizer', citadela_paths()->url->js . '/customizer.js', array( 'customize-preview' ), filemtime(citadela_paths()->dir->js . '/customizer.js'), true );
}
add_action( 'customize_preview_init', 'citadela_theme_customize_preview_js' );


