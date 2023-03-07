<?php

/*
 * Citadela Listing Ajax functions
 *
 */


abstract class CitadelaDirectoryFrontendAjax
{


	/**
	 * Sends JSON response
	 * @param  mixed $data Data to be json encoded and send
	 * @return void       exits
	 */
	public function sendJson($data)
	{
		wp_send_json_success($data);
	}



	/**
	 * Sends JSON Error response
	 * @param  mixed $data Error mesages or data to be json encoded and send
	 * @return void        exits
	 */
	public function sendErrorJson($data)
	{
		wp_send_json_error($data);
	}


}
