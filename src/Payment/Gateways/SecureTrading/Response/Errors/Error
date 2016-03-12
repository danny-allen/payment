<?php

/**
 * Notes:
 * 	- Does not care what type of Response has been made.
 * 	- Only cares about Secure trading Responses.
 */

namespace Dao\Payment\Gateways\SecureTrading\Response\Errors;

use Exception;
use Dao\Payment\Helpers\Validate;

class Error {
	
	public function __construct($code = null, $message = null) {

		//validate and set message
		Validate::string($message);
		$this->message = $message;

		//validate and set code
		Validate::int($code);
		$this->code = $code;
	}

	public function message() {
		return $this->message;
	}

	public function code() {
		return $this->code;

	}
}