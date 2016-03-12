<?php

/**
 * Notes:
 * 	- Should not care which payment gateway is being used.
 * 	- Does not care about what request is being called as long as it exists for the payment gateway.
 */

namespace Dao\Payment\Gateways;

use Exception;
use Dao\Payment\Helpers\Validate;
use Dao\Payment\Gateways\SecureTrading\Requests\Request;
use Dao\Payment\Gateways\SecureTrading\Response\Response;

abstract class Gateway {

	/**
	 * $requestNamespace
	 *
	 * The namespace that the request type classes reside under.
	 * 
	 * @var string
	 */
	protected $requestNamespace = 'Dao\\Payment\\';


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: ";


	/**
	 * $newRequest
	 *
	 * Determines if we're dealing with a new set of requests.
	 * 
	 * @var boolean
	 */
	private $newRequest = true;


	/**
	 * $debug
	 *
	 * Whether or not we're in debug mode.
	 * 
	 * @var boolean
	 */
	public $debug = false;



	/**
	 * request
	 *
	 * Make a request via the Secure trading API
	 * 
	 * @param  string 	$type   	The type of request that needs to be made.
	 * @param  complex 	$options 	the options to store against the request object.
	 * @return string         		The response from the request object.
	 */
	public function request($type, $options) {

		//get a base request
		$this->base = $this->getRequest();

		//set the options (from settings)
		$this->base->options($this->settings);

		//validate the options
		$this->base->validate();

		//build the base xml
		$this->base->build();

		//make sure request type is available
		$childRequest = $this->getRequest($type, $this->base); 

		//set options for request
		$childRequest->options($options);

		//validate the options
		$childRequest->validate();

		//build the request
		$this->base = $childRequest->build();

		//send the request
		$response = $this->sendRequest();
		var_dump($response);
		die('laaa');
		
		//return the response
		return $response;
	}


	/**
	 * sendRequest
	 *
	 * All sub requests have been retrieved, and can now be sent.
	 *
	 * Afterwards, we set newRequest to true, so that a new transaction can be made,
	 * if required (not yet tested!).
	 */
	public function sendRequest() {

		//get the output
		$requestQuery = $this->base->output();

		//make the request
		$this->request = new Request($this->settings);

		//set debug
		$this->request->debug = $this->debug;

		//allow new requests again
		$this->newRequest = true;

		//get result
		$result = $this->request->make($requestQuery);

		//return the request
		return $response = new Response($result);
	}



	/**
	 * getRequest
	 *
	 * Checks if the request type class exists. If so, it is instantiated and returned.
	 * Otherwise an exception is thrown.
	 * 
	 * @param  string	$requestType	The type of request to instantiate.
	 * @param  string	$params			The params to instantiate the request with
	 * @return object 					The class of the request type.
	 */
	public function getRequest($requestType = 'Base', $params = null) {

		//validate the request type, make sure it's a string
		Validate::string($requestType, $this->errorPrefix."request type");

		if($requestType == 'Base'){

			//set the class name we're looking for
			$class = $this->requestNamespace . "\\Base";
		}else{

			//set the class name we're looking for
			$class = $this->requestNamespace . "\\" . $requestType . "\\" . $requestType;
		}

		//check if the class exists
		if(!class_exists($class)) {

			//if it doesnt exist, throw an error
			throw new Exception($this->errorPrefix.'request type unknown.');
		}

		//return the instantiated request, passing in the base XML
		return new $class($params);
	}


	/**
	 * setting
	 *
	 * Regsiter a setting and value pair against this object.
	 * 
	 * @param  string 	$setting 	Name of the setting to add to the Gateway object.
	 * @param  complex 	$value  	The value to be stored against the setting.
	 */
	public function setting($setting = null, $value) {

		//validate string
		Validate::string($setting, $this->errorPrefix."setting");

		//add the setting
		$this->settings[$setting] = $value;
	}
}