<?php

/**
 * Notes:
 * 	- Cares about which payment gateway is being used (top level)
 * 	- Cares about which methods are being used (top level)
 */

namespace Dao;

use Exception;
use Dao\Payment\Helpers\Validate;

class Payment {

	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: Payment - ";


	/**
	 * $gatewayNamespace
	 *
	 * The gateway namespace to call the payment gateways from.
	 * 
	 * @var string
	 */
	protected $gatewayNamespace = "Dao\\Payment\\Gateways\\";


	/**
	 * __construct
	 *
	 * Check if the payment gateway exists and instantiate it.
	 * 
	 * @param string $gateway The payment gateway to instantiate.
	 */
	public function __construct($gateway = null) {

		//validate string
		Validate::string($gateway, $this->errorPrefix.'gateway');

		//set the class name we're looking for
		$class = $this->gatewayNamespace.$gateway."\\".$gateway;

		//check if the class exists
		if(class_exists($class)) {

			//instantiate the gateway
			$this->gateway = new $class();

		} else {

			//if it doesnt exist, throw an error
			throw new Exception($this->errorPrefix.'gateway not found.');
		}
	}


	/**
	 * setting
	 *
	 * Add a setting.
	 * 
	 * @param  string 		$option 	The setting name.
	 * @param  complex  	$value  	The value to store against the setting name.
	 */
	public function setting($option, $value = null) {
		$this->gateway->setting($option, $value);
	}


	/**
	 * settings
	 *
	 * Add settings in bulk.
	 * 
	 * @param  array  $options The options to add. Uses the setting method.
	 */
	public function settings(array $options) {
		
		//add multiple settings
		foreach ($options as $option => $value){
			$this->setting($option, $value);
		}
	}


	/**
	 * request
	 *
	 * Make the payment gateway request. At this point we're not bothered about
	 * whether it exists or not. The individual Payment Gateway can determine
	 * this.
	 * 
	 * @param  	string 		$request 	The name of the type of request to make.
	 * @param  	array  		$options 	array of options to pass to the request method.
	 * @param  	function  	$callback	Option callback function to handle the response.
	 * @return  string 					Returns the response as a string if no callback is specified.
	 */
	public function request($request, $options = array(), $callback = null) {

		//validate string
		Validate::string($request, $this->errorPrefix.'request');

		//make the request
		$response = $this->gateway->request($request, $options);

		//check for callback
		if(is_callable($callback)){
			//call callback with response.
			$callback($response);

		}else{
			//else return the response
			return $response;
		}
	}
}