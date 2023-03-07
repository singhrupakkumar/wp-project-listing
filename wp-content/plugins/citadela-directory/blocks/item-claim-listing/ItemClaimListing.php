<?php

namespace Citadela\Directory\Blocks;

use Citadela\Directory\ClaimListing;

class ItemClaimListing extends Block
{
    protected static $slug = 'item-claim-listing';

    function __construct() {
        parent::__construct();
    }

    public static function renderCallback($attributes, $content) {
        if (is_admin() || !ClaimListing::enabled()) {
            return;
        }
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $classes[] = "align-{$attributes['align']}";
        $classes[] = "{$attributes['style']}-style";
        if ($attributes['textColor']) $classes[] = "custom-text-color";
        if ($attributes['style'] != 'text' && $attributes['bgColor']) $classes[] = "custom-background-color";
        if ($attributes['style'] != 'text' && isset( $attributes['borderRadius'])) $classes[] = "custom-border-radius";
        
        $styles = [];
        if( $attributes['textColor'] ) $styles[] = "color: " . esc_attr( $attributes['textColor'] ) . ";";
        if ($attributes['style'] != 'text') {
            if ($attributes['bgColor']) $styles[] = "background-color: " . esc_attr( $attributes['bgColor']) . ";";
            if (isset($attributes['borderRadius'])) $styles[] = "border-radius: " . esc_attr( $attributes['borderRadius'] ) . "px;";
        }
        $buttonText = $attributes['text'] !== '' ? $attributes['text'] : self::$attributes['text']['default'];
        global $post;
        $user = wp_get_current_user();
        if (!$user->ID || ($user->ID && !ClaimListing::hasActiveSubscription($user->ID))) {
            $subscriptions = ClaimListing::getSubscriptions();
            foreach ($subscriptions as $subscription) {
                $subscription->price = get_woocommerce_currency_symbol() . \WC_Subscriptions_Product::get_price_string($subscription->ID);
            }
        }
        $status = ClaimListing::getStatus($post->ID);
        wp_enqueue_script('citadela-directory-claim-listing', plugin_dir_url(__FILE__). '/src/frontend.js', [], filemtime(__DIR__ . '/src/frontend.js'));
        ob_start();
        if (!($status === 'Approved' && empty($attributes['notificationAlready'])) && !($status === 'Pending' && empty($attributes['notificationPending']))) { ?>
        <div id="citadela-claim-listing" class="wp-block-citadela-blocks ctdl-item-claim-listing closed <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <?php if (empty($status) || $status === 'Unclaimed') { ?>
                <div id="citadela-claim-listing-button" class="claim-listing-button">
                    <button class="claim-listing-btn" style="<?php echo implode( "", $styles ); ?>"><?php echo wp_kses_data( $buttonText ); ?></button>
                </div>
                <div id="citadela-claim-listing-form" class="claim-listing-form <?php echo esc_attr( \CitadelaDirectoryRecaptcha::$activeCaptcha ? 'active-captcha' : '' ); ?>" style="display: none;">
                    <?php if (!empty($attributes['formTitle'])) { ?>
                    <header class="citadela-block-header">
                        <div class="citadela-block-title">
                            <h2><?php echo esc_html($attributes['formTitle']); ?></h2>
                        </div>
                    </header>
                    <?php } ?>
                    <?php if (!empty($attributes['formDescription'])) { ?>
                    <div class="citadela-block-description">
                        <p><?php echo wp_kses_data( $attributes['formDescription'] ); ?></p>
                    </div>
                    <?php } ?>
                    <div class="citadela-block-form">
                        <form>
                            <input name="id" type="hidden" value="<?php global $post; echo esc_attr( $post->ID ); ?>">
                            <?php if ($user->ID) { ?>
                                <p><?php esc_html_e('Claim as:', 'citadela-directory'); ?> <b class="claim-listing-user"><?php echo esc_html( $user->get('user_login') . ' (' . $user->get('user_email') . ')' ); ?></b></p>
                                <?php if (!empty($attributes['formLoggedIn'])) { ?>
                                    <p><?php echo esc_html($attributes['formLoggedIn']); ?></p>
                                <?php } ?>
                                <?php if (!empty($subscriptions)) { ?>
                                    <div class="input-container product">
                                        <select name="product">
                                            <?php foreach ($subscriptions as $subscription) { ?>
                                            <option value="<?php echo $subscription->ID; ?>"><?php echo $subscription->post_title . ' (' . $subscription->price . ')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="input-container email">
                                    <label><?php echo esc_html($attributes['formEmail']); ?></label>
                                    <input type="text" name="email" placeholder="<?php echo esc_attr($attributes['formEmail']); ?>">
                                </div>
                                <div class="input-container username">
                                    <label><?php echo esc_html($attributes['formUsername']); ?></label>
                                    <input type="text" name="username" placeholder="<?php echo esc_attr($attributes['formUsername']); ?>">
                                </div>
                                <?php if (!empty($subscriptions)) { ?>
                                    <div class="input-container product">
                                        <select name="product">
                                            <?php foreach ($subscriptions as $subscription) { ?>
                                            <option value="<?php echo $subscription->ID; ?>"><?php echo $subscription->post_title . ' (' . $subscription->price . ')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($attributes['formTerms'])) { ?>
                                <div class="input-container terms">
                                    <input type="checkbox" name="terms" value="terms">
                                    <label for="terms"><?php echo esc_html( $attributes['formTerms'] ); ?></label>
                                </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="input-container claim-listing-button">
                                <button id="citadela-claim-listing-button-claim" style="<?php echo implode( "", $styles ); ?>" type="submit"><?php echo esc_html($attributes['formSubmit']); ?></button>
                                <i class="fa fa-sync fa-spin" style="display: none;"></i>
                            </div>
                            <div class="data-messages">
                                <div id="citadela-claim-listing-notification-empty" style="display: none;"><?php esc_html_e('Please fill in all the required fields'); ?></div>
                                <div id="citadela-claim-listing-notification-already" style="display: none;"><?php echo esc_html($attributes['notificationAlready']); ?></div>
                                <div id="citadela-claim-listing-notification-pending" style="display: none;"><?php echo esc_html($attributes['notificationPending']); ?></div>
                                <div class="msg msg-success" style="display: none;">
                                    <p><?php esc_html_e('Item has been claimed', 'citadela-directory'); ?></p>
                                </div>
                                <div class="msg msg-error" style="display: none;">
                                    <p></p>
                                </div>
                                <div class="msg msg-error-server" style="display: none;">
                                    <p><?php esc_html_e('Server encountered an error. If problem persists, contact website administrator, please.', 'citadela-directory'); ?></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } else { ?>
                <div class="claim-listing-notification">
                    <?php
                    if ($status === 'Approved' && !empty($attributes['notificationAlready'])) {
                        echo esc_html($attributes['notificationAlready']);
                    }
                    if ($status === 'Pending' && !empty($attributes['notificationPending'])) {
                        echo esc_html($attributes['notificationPending']);
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
        <?php }
        return ob_get_clean();
    }

}