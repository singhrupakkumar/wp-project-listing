<?php

namespace Citadela\Pro;

class WooCommerce {

	public static $isActive = false;

	static function run() {
		if( ! class_exists( 'woocommerce') ) return;

		self::$isActive = true;
		add_filter( 'body_class', [ __CLASS__, 'body_class' ] );
		add_action( 'citadela_render_woocommerce_minicart', [ __CLASS__, 'render_minicart' ] );
		add_filter( 'woocommerce_add_to_cart_fragments', [ __CLASS__, 'woocommerce_add_to_cart_fragments' ] );
		add_filter( 'woocommerce_widget_cart_is_hidden', [ __CLASS__, 'woocommerce_widget_cart_is_hidden' ] );
		
	}

	public static function body_class( $classes ) {
		$classes[] = 'header-with-cart';

		return $classes;
	}

	static function render_minicart() {

		//do not render cart on Cart or Checkout pages
		//if( ! self::is_visible_cart() ) return;

		$classes = [ 'citadela-woocommerce-minicart' ];
		if( self::cart_is_empty() ) $classes[] = 'is-empty';

		?>
		
		<div class="<?php echo implode(' ', $classes); ?>" style="display: inline-block; font-size: initial;">
			<div class="inner-wrapper">
				<div class="cart-header">
					<div class="cart-icon"><?php echo self::cart_icon(); ?></div>
					<div class="cart-count"><span><?php echo self::cart_count(); ?></span></div>
				</div>
				<div class="cart-content">
					<?php echo self::cart_content(); ?>
				</div>

			</div>
		</div>

		<?php
	}

	private static function cart_content() {
		ob_start();
		
		the_widget('WC_Widget_Cart', array(
				'citadela-woocommerce-minicart-widget' => true,
			)
			, array(
				'before_title' => '',
				'after_title' => ''
		));

		$widget = ob_get_clean();

		return $widget;
	}

	public static function woocommerce_add_to_cart_fragments( $fragments ){
		$fragments['.citadela-woocommerce-minicart .cart-count span'] = '<span>' . self::cart_count() . '</span>';
		return $fragments;
	}

	private static function cart_icon( $type = 'fontawesome' ) {
		$icon = '<i class="fas fa-shopping-basket"></i>';
		return $icon;
	}

	private static function cart_count() {
		global $woocommerce;
		return $woocommerce->cart->get_cart_contents_count();
	}

	public static function cart_is_empty() {
		global $woocommerce;
		return $woocommerce->cart->get_cart_contents_count() == 0 ? true : false;
	}

	public static function is_visible_cart() {
		if ( is_cart() || is_checkout() ) {
			return false;
		}
		return true;
	}
	
	public static function woocommerce_widget_cart_is_hidden() {
		return false;
	}
}
