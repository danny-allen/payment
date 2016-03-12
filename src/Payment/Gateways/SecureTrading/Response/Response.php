<?php

/**
 * Notes:
 * 	- Does not care what type of Response has been made.
 * 	- Only cares about Secure trading Responses.
 */

namespace Dao\Payment\Gateways\SecureTrading\Response;

use Exception;
use Dao\DOMDocument;
use Dao\Payment\Helpers\Validate;
use Dao\Payment\Gateways\SecureTrading\Response\Error;

class Response {
	

	public function __construct($response = null) {

		//remove headers (or seperate)
		list($header, $xmlNode) = explode("\r\n\r\n", $response, 2);

		//response
		$this->response = $xmlNode;

		//parse response
		//how the XML should have been read through this app *sigh*
		$this->dom = new DOMDocument();
		$this->loadXML($this->response);
	}

	public function transactionReference() {

		//find the transaction ref
		$node = $dom->getElementsByTagName('transactionreference');

		//return it
		return ($node->length > 0)? $node->item(0)->nodeValue : null;
	}

	public function error() {

		$errors 	= $dom->getElementsByTagName('error'); //should always exist - even on success
		$errorCode 	= $errors->item(0)->getElementsByTagName('code')->item(0)->nodeValue; //0 on success
		
		//check for fail, 0 = success
		if($errorCode !== "0"){
			$message = $errors->item(0)->getElementsByTagName('message')->item(0)->nodeValue;
			$error = new Error($errorCode , $message);
			return $error;
		}else {
			return false;
		}
	}




}