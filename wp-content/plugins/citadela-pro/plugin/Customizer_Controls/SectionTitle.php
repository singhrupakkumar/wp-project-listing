<?php

class SectionTitleControl extends WP_Customize_Control {
   
   public $type = 'section_title';
   
   public $args;

   public function render_content() {
	   ?>
		   
		   <div class="citadela-section-title">
		   		<?php if( isset( $this->args['title'] ) ) : ?>
		   			<div class="customize-control-title"><?php esc_html_e( $this->args['title'] ); ?></div>
		   		<?php endif; ?>

		   		<?php if( isset( $this->args['description'] ) ) : ?>
			   		<p class="customize-control-description"><?php esc_html_e( $this->args['description'] ); ?></p>
			   	<?php endif; ?>
		   </div>
	   
	   <?php
   }

}