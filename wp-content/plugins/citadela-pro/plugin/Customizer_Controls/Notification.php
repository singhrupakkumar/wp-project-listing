<?php

class NotificationControl extends WP_Customize_Control {
   
   public $type = 'notification';
   
   public $args;

   public function render_content() {
	   ?>
		   
		   <div class="citadela-notification">
		   		<?php echo $this->args['message']; ?>
		   </div>
	   
	   <?php
   }

}