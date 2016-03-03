<?php

/**
 * Notes:
 * 	- Does not care what type of request has been made.
 * 	- Only cares about Secure trading requests.
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests;

use Exception;
use DannyAllen\Plug;
use Dao\Payment\Helpers\Validate;

abstract class Request {

	/**
	 * $alias
	 *
	 * Secure Trading account alias.
	 *
	 * @var string
	 */
	protected $alias;


	/**
	 * $siteReference
	 *
	 * Secure Trading site reference.
	 *
	 * @var string
	 */
	protected $siteReference;


	/**
	 * $apiIp
	 *
	 * The IP address the STAPI is running on.
	 * 
	 * @var string
	 */
	protected $apiIp = '127.0.0.1';


	/**
	 * $apiPort
	 *
	 * The port number the STAPI is running on.
	 * 
	 * @var integer
	 */
	protected $apiPort = 5000;


	/**
	 * $apiVersion
	 *
	 * Secure Trading API version.
	 *
	 * @var string
	 */
	protected $apiVersion;


	/**
	 * $currencyCode
	 *
	 * Type of currency used for the request.
	 *
	 * @var string
	 */
	protected $currencyCode;


	/**
	 * $accountType
	 *
	 * Type of account making the request (ECOM).
	 *
	 * @var string
	 */
	protected $accountType;


	/**
	 * $requestXML
	 *
	 * The XML to be returned.
	 * 
	 * @var string
	 */
	protected $requestXML;


	/**
	 * $errorPrefix
	 *
	 * Message to prefix to errors.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading - ";



	/**
	 * build
	 *
	 * Request objects must declare the build method. This builds the XML.
	 * 
	 * @return string  	The XML to return.
	 */
	abstract public function build();


	/**
	 * options
	 *
	 * Only allow options to be set if the property exists on the class inheriting.
	 * 
	 * @param  array 	$options 	The options to check for.
	 */
	public function options($options) {

		//loop the options and add them to the object
		foreach($options as $option => $value){

			//check option is a string
			Validate::string($option);

			//get the child class, to check properties against
			$class = get_class($this);
			
			//check property value
			if(property_exists($class, $option) || property_exists($this, $option)){
				$this->{$option} = $value;


			}else{
				//property does not exist, either in the parent or the extending class.
				throw new Exception('The '.(string) $option.' property does not exist.');
			}
		}
	}


	/**
	 * make
	 *
	 * Make the request. Write the XML generated from the build function to the socket.
	 * 
	 * @return string  	The output from the socket, based on the data sent.
	 */
	public function make() {

		//check for request XML
		if(!isset($this->requestXML)){
			throw new Exception($this->errorPrefix."requestXML is empty.");
		}

		//make sure the request XML is a string - it should be by this point.
		Validate::string($this->requestXML);

		//instantiate a plug - creates a socket.
		$plug = new Plug();

		//connect the plug.
		$plug->connect($this->apiIp, $this->apiPort);

		//switch it on and get the output.
		$output = $plug->on($this->requestXML);

		//switch it off, we're done.
		$plug->off();
		
		//return the response from the socket
		return $output;
	}	
}