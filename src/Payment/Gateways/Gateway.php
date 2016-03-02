<?php

/**
 * Notes:
 * 	- Should not care which payment gateway is being used.
 * 	- Does not care about what request is being called as long as it exists for the payment gateway.
 */

namespace DannyAllen\Payment\Gateways;

use Exception;
use DannyAllen\Payment\Helpers\Validate;

class Gateway {

	/**
	 * $requestNamespace
	 *
	 * The namespace that the request type classes reside under.
	 * 
	 * @var string
	 */
	protected $requestNamespace = 'DannyAllen\\Payment\\';

	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading - ";

	//need our settings declared - vars or array?
	public $accountType = 'ECOM';
	public $currencyCode = 'GBP';



	public function __construct($version = '3.67'){
		//what we doing here? :/
	}


	/**
	 * request
	 *
	 * Make a request via the Secure trading API
	 * 
	 * @param  string 	$type   	The type of request that needs to be made.
	 * @param  complex 	$params 	
	 * @return [type]         		[description]
	 */
	public function request($type, $params) {

		//make sure request type is available
		$request = $this->_initiateRequest($type);

		$response = "called";

		//build the response
		//$request->build($params);

		//make the response
		//$response = $request->make();

		return $response;
	}


	/**
	 * _initiateRequest
	 *
	 * Checks if the request type class exists. If so, it is instantiated and returned.
	 * Otherwise an exception is thrown.
	 * 
	 * @param  string $requestType 		The type of request to instantiate.
	 * @return object 				    The class of the request type.
	 */
	public function _initiateRequest($requestType) {

		//validate the request type, make sure it's a string
		Validate::string($requestType, $this->errorPrefix."request type");

		//set the class name we're looking for
		$class = $this->requestNamespace."\\".$requestType;

		//check if the class exists
		if(!class_exists($class)) {

			//if it doesnt exist, throw an error
			throw new Exception($this->errorPrefix.'request type unknown.');
		}

		//instantiate the request
		return $request = new $class();
	}



	/**
	 * setting
	 *
	 * Regsiter a setting and value pair against this object.
	 * @param  [type] $option [description]
	 * @param  [type] $value  [description]
	 * @return [type]         [description]
	 */
	public function setting($option = null, $value) {

		//validate string
		Validate::string($option, $this->errorPrefix."option");

		//add the option
		$this->{$option} = $value;
	}
}