<?php

namespace Citadela\Customizer\Controls;

class CitadelaUpsellControl extends \WP_Customize_Control {

    public $type = 'citadela-control-upsell';

    public $cta_url = '';
    public $listing_image = '';
    public $business_image = '';

    public function __construct($manager, $id, $args)
    {
        parent::__construct($manager, $id, $args);
    }



    public function json() {
        $json = parent::json();

        $json['cta_url'] = $this->cta_url;
        $json['listing_image'] = $this->listing_image;
        $json['business_image'] = $this->business_image;

		return $json;
	}



    public function content_template() {
        ?>
        <div class="ctdl-info">
        	<p><?php esc_html_e('Citadela is a free multi-purpose WordPress theme created by AitThemes. You can use it without any restrictions on your commercial or non-commercial website.', 'citadela'); ?></p>
        	<p><strong><?php esc_html_e('We have also created premium versions of Citadela that will shift your website to the whole new level.', 'citadela'); ?></strong></p>
		</div>

        <div class="ctdl-pro dir">
        	<img src="{{{ data.listing_image }}}">
        </div>
        <div class="ctdl-pro blocks">
        	<img src="{{{ data.business_image }}}">
        </div>

        <a href="{{{ data.cta_url }}}" class="button button-primary ctdl-pro-button"><?php esc_html_e('Learn more', 'citadela'); ?></a>
        <?php
    }
}
