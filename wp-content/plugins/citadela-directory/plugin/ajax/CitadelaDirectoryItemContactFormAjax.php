<?php

/*
 * Citadela Listing Ajax functions - Item Contact Form submit
 *
 */


class CitadelaDirectoryItemContactFormAjax extends CitadelaDirectoryFrontendAjax
{


	/**
	 * wpajax_ prefix required to set ajax actions
	 */
	public function wpajax_send()
	{
		$result = new stdClass();
		if( isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']){

			//check captcha
			$token = $_POST['g-recaptcha-response'];
			$response = CitadelaDirectoryRecaptcha::verify($token);
			$result->captcha_response = $response;
			if($response->success == false){
				$this->sendErrorJson(array(
						'message' => "Captcha check failed!",
						'recaptha_response' => $result->captcha_response,
				));
			}

		}

		//build email message
		$matches = array();
		preg_match_all('/{([^}]*)}/', $_POST['response-email-content'], $matches);
		foreach($matches[1] as $i => $match){
			$_POST['response-email-content'] = str_replace($matches[0][$i], $_POST[$match], $_POST['response-email-content']);
		}
		$_POST['response-email-content'] = str_ireplace(array("\r\n", "\n"), "<br />", $_POST['response-email-content']);

		//set sender name
		$senderName = isset($_POST['response-email-sender-name']) ? $_POST['response-email-sender-name'] : '';

		//build message header
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'Reply-To: '.$_POST['user-name'].' <'.$_POST['user-email'].'>',
			'From: '.$senderName.' <'.$_POST['response-email-sender-address'].'>', 
		);

		//send an email
		$result->email_response = $email_result = wp_mail($_POST['response-email-address'], $_POST['user-subject'], $_POST['response-email-content'], $headers, null);
		

		if($email_result == true){
			$this->sendJson(array(
					'message' => sprintf("Mail sent to %s", $_POST['response-email-address']),
					'result' => $result,
			));
		} else {
			$this->sendErrorJson(array('message' => "Mail failed to send"));
		}
	}
}