<?php

namespace Citadela\Directory\Blocks;

class DirectoryMembershipContent extends Block {

    protected static $slug = 'directory-membership-content';

    public static function renderCallback($attributes, $content) {
        if ( is_admin() ) {
            return;
        }
        $plugin_instance = \CitadelaDirectory::getInstance();
        $subscriptions_instance = $plugin_instance->Subscriptions_instance;
        $blockTitle = $attributes['title'];
        $classes = [];
        if( isset( $attributes['className'] ) ){ $classes[] = $attributes['className']; }; 
        $can_render = false;
        $is_googlebot = $plugin_instance->is_googlebot( $plugin_instance->get_the_user_ip() );

        // administrator can always see the content
        if( current_user_can('administrator') ){
            $can_render = true;
            if( $attributes['contentFor'] == 'non-membership' ){
                $classes[] = 'ctdl-non-members';
            }
            if( $attributes['contentFor'] == 'active-membership' ){
                $classes[] = 'ctdl-active-members';
            }
        }else{
            if( $attributes['contentFor'] == 'non-membership' && ! $subscriptions_instance->user_has_active_any_woo_subscription() && ! $is_googlebot ){
                $classes[] = 'ctdl-non-members';
                $can_render = true;
            }
    
            if( $attributes['contentFor'] == 'active-membership' ){
                $classes[] = 'ctdl-active-members';
                if( $is_googlebot ){
                    $can_render = true;
                }else{
                    if( $attributes['membership'] == "0" ){
                        if( $subscriptions_instance->user_has_active_any_woo_subscription() ){
                            $can_render = true;
                        }
                    }else{
                        $user_active_subscription = $subscriptions_instance->get_user_active_any_woo_subscription();
                        if( $user_active_subscription && strval($user_active_subscription) === $attributes['membership'] ){
                            $can_render = true;
                        }
                    }
                }
            }
        }
        
        if( ! $can_render ){
            return;
        }
        
        
        ob_start();
        ?>

        <div class="wp-block-citadela-blocks ctdl-directory-membership-content <?php echo esc_attr( implode( " ", $classes ) );?>" >

            <?php if($blockTitle) : ?>
            <header class="citadela-block-header">
                <div class="citadela-block-title">
                    <h2><?php echo esc_html( $blockTitle ); ?></h2>
                </div>
            </header>
            <?php endif; ?>
            
            <?php echo $content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            
        </div>
        <?php

        return ob_get_clean();
    }

}