function submitItemContactForm(e){
	e.preventDefault();

	var $form = jQuery("#"+e.target.id);
	var activeCapcha = $form.hasClass('active-captcha');

	if(activeCapcha){
		grecaptcha.ready(function() {
			grecaptcha.execute(citadela.keys.recaptchaSiteKey, {action: 'item_contact_form'}).then(function(token) {
				// add token to form
				$form.find('input.citadela-recaptcha-token').val(token);
				sendContactForm($form);
			});;
		});
	}else{
		sendContactForm($form);
	}



	

}

function sendContactForm($form){
	var $inputs = $form.find('input, textarea');
	var $submitButton = $form.find("button.item-detail-submit-form");
	var $loader = $form.find("i.fa");
	var $messages = $form.find('.data-messages');
	var mailCheck = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	var mailParsed = $form.find('input[name=user-email]').val();

	// check for empty inputs -- all inputs must be filled
	$isEmptyInput = false;
	$inputs.each(function(){
		if( jQuery(this).val() == "" && !jQuery(this).hasClass('citadela-recaptcha-token') ){
			$isEmptyInput = true;
		}
	});
	
	if( !$isEmptyInput && mailCheck.test(mailParsed) ){

		$loader.fadeIn('slow');
		var data = {};
		$inputs.each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		});
		
		$submitButton.attr('disabled', true);
		citadela.ajax.post('item-contact-form:wpajax_send', data).done(function(data){
			if(data.success == true){
				$messages.find('.msg-success').fadeIn('fast').delay(3000).fadeOut("fast", function(){
					$form.find('input[type=text], input.citadela-recaptcha-token, textarea').each(function(){
						jQuery(this).attr('value', "");
					});
					$submitButton.removeAttr('disabled');
				});
			} else {
				$messages.find('.msg-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
				$submitButton.removeAttr('disabled');
			}
			$loader.fadeOut('slow');
		}).fail(function(xhr, status, error){
			$messages.find('.msg-error-server').fadeIn('fast').delay(3000).fadeOut("fast");
			$submitButton.removeAttr('disabled');
			$loader.fadeOut('slow');
		});
	}else{
		//inputs validation problem
		$messages.find('.msg-error-user').fadeIn('fast').delay(3000).fadeOut("fast");
		
	}
}