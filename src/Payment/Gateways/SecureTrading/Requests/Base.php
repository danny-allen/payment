<?php

/**
 * Notes:
 * 	- Only cares about the Base type request for SecureTrading
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests;

use Exception;
use DOMDocument;
use DateTime;
use Dao\XMLHandler;
use Dao\Payment\Helpers\Validate;

class Base {

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
	 * __construct
	 *
	 * Builds the initial XML document to add to for the request.
	 */
	public function __construct() {

		//make the XML object
		$this->dom = new DOMDocument('1.0', 'utf-8');

		//make the XML object
		$this->xml = new XMLHandler($this->dom);
	}


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
	 * build
	 *
	 * Build the main content into the Base request XML.
	 * 
	 * @return string 	The XML to return.
	 */
	public function build() {

		//requestblock attributes
		$this->requestblock = $this->xml->dom->createElement('requestblock');
		$this->requestblock->setAttribute('version', $this->apiVersion);

		//alias node
		$alias = $this->xml->dom->createElement('alias', $this->alias);

		//create request block with children
		$this->xml->createChildElements($this->requestblock, array(
			'alias' => $alias
		));

		//append the request block
		$this->xml->dom->appendChild($this->requestblock);

		//return it.
		return $this->xml;
	}
	

	/**
	 * output
	 *
	 * Output the XML as a string.
	 * 
	 * @return string Output the XML as a string.
	 */
	public function output() {

		//set the requestXML
		$this->requestXML = $this->xml->dom->saveXML();

		//store the request XML
		return $this->requestXML;
	}


	/**
	 * validate
	 *
	 * Runs all validation methods.
	 */
	public function validate() {

		//run the validation methods
		$this->validateAccountType();
	}


	/**
	 * validateAccountType
	 *
	 * Checks account type is ECOM.
	 */
	private function validateAccountType() {

		//set allowed values
		$allowed = array('ECOM');

		//make sure it's a string
		Validate::string($this->accountType, $this->errorPrefix.'account type');

		//make sure it's allowed
		if(!in_array($this->accountType, $allowed)) {
			throw new Exception($errorPrefix."account type is not valid.");
		}
	}

}