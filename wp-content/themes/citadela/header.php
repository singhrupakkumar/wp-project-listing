<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php 
$theme_instance = Citadela_Theme::get_instance(); 
$header_classes = [ 'loading' ];
$site_branding_classes = [];

$sticky_header_desktop = get_theme_mod( 'citadela_setting_stickyHeaderDesktop', 'none' );
$sticky_header_mobile = get_theme_mod( 'citadela_setting_stickyHeaderMobile', 'burger-menu-only' );
$enable_sticky_header = $sticky_header_desktop == 'sticky-header-desktop-full' || $sticky_header_mobile == 'sticky-header-mobile-full';

$hide_sitetitle_tagline_desktop = get_theme_mod('citadela_setting_hideSitetitleAndTaglineDesktop', 'none' );
if( $hide_sitetitle_tagline_desktop == 'hide-title-and-tagline' ){
	$site_branding_classes[] = "hide-textlogo-desktop";
}
if( $hide_sitetitle_tagline_desktop == 'hide-tagline' ){
	$site_branding_classes[] = "hide-tagline-desktop";
}
$hide_sitetitle_tagline_mobile = get_theme_mod('citadela_setting_hideSitetitleAndTaglineMobile', 'none' );
if( $hide_sitetitle_tagline_mobile == 'hide-title-and-tagline' ){
	$site_branding_classes[] = "hide-textlogo-mobile";
}
if( $hide_sitetitle_tagline_mobile == 'hide-tagline' ){
	$site_branding_classes[] = "hide-tagline-mobile";
}

if( $sticky_header_desktop == 'sticky-header-desktop-full' ){
	if( $theme_instance->get_pro_theme_mod( 'citadela_setting_hideSitetitleOnFullStickyDesktop', false ) ){
		$site_branding_classes[] = 'hide-title-desktop';		
	}
}
if( $sticky_header_mobile == 'sticky-header-mobile-full' ){
	if( $theme_instance->get_pro_theme_mod( 'citadela_setting_hideSitetitleOnFullStickyMobile', false ) ){
		$site_branding_classes[] = 'hide-title-mobile';		
	}
}

?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'citadela' ); ?></a>
	<?php
		
		if( get_theme_mod( 'citadela_setting_headerFullwidth' ) ){
			$header_classes[] = 'fullwidth-header';
		}
		if( $theme_instance->get_pro_theme_mod( 'citadela_setting_logoAlignLeftMobile', false ) ){
			$header_classes[] = 'logo-align-left-mobile';
		}
		
	?>
	<?php if( $enable_sticky_header ): ?> 
		<div class="sticky-header-wrapper">
	<?php endif; ?>

		<header id="masthead" class="site-header <?php echo esc_attr( implode(' ', $header_classes ) ); ?>">
			<div class="grid-main">

				<div 
					class="site-branding <?php echo esc_attr( implode(' ', $site_branding_classes ) ); ?>" 
					<?php do_action( 'citadela_pro_site_branding_data' ); ?>
				>
					<?php if( get_theme_mod( 'custom_logo' ) != "" ): ?>
						<div 
							class="logo-wrapper" 
							<?php do_action( 'citadela_pro_logo_wrapper_data' ); ?>
						>
							<?php do_action( 'citadela_render_header_logo' );	?>
						</div>
					<?php endif; ?>

					<div class="text-logo">

						<?php
						if ( is_front_page() && is_home() ) :
							?>
							<h1 class="site-title" <?php do_action( 'citadela_pro_logo_font_size_data' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php
						else :
							?>
							<p class="site-title" <?php do_action( 'citadela_pro_logo_font_size_data' ); ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
							<?php
						endif;

						if ( get_bloginfo( 'description', 'display' ) || is_customize_preview() ) :
							?>
							<p class="site-description"><?php bloginfo( 'description' ); ?></p>
						<?php endif; ?>
					</div><!-- .text-logo -->
				</div><!-- .site-branding -->
				
				<?php $is_header_menu = has_nav_menu( 'main-menu' ); ?>
				
				<nav id="site-navigation" class="main-navigation menu-hidden <?php if( ! $is_header_menu ) echo esc_attr( 'no-main-menu' ) ?>">
					<?php
					if( $is_header_menu ) {
						wp_nav_menu( array(
							'theme_location' => 'main-menu',
							'menu_id'        => 'main-menu',
						) );
					}
					?>
				
				<?php do_action( 'citadela_render_woocommerce_minicart' ); ?>
				
				</nav><!-- #site-navigation -->


			</div><!-- .grid-main -->
		</header><!-- #masthead -->

	<?php if( $enable_sticky_header ): ?> 
		</div>
	<?php endif; ?>

	<div id="content" class="site-content">
