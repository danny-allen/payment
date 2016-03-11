<?php

/**
 * Notes:
 * 	- Only cares about the Refund type request for SecureTrading
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests\Refund;

use Exception;
use DomElement;
use DOMDocument;
use DateTime;
use Dao\XMLHandler;
use Dao\Payment\Helpers\Validate;
use Dao\Payment\Gateways\SecureTrading\Requests\Base;

class Refund extends Base {


	/**
	 * $requestType
	 *
	 * The type of request to be made to Secure Trading.
	 * 
	 * @var string
	 */
	protected $requestType = 'REFUND';


	/**
	 * $amount
	 *
	 * The amount to be used in the transaction. It's passed in pence.
	 * 
	 * @var int
	 */
	protected $amount = 0;


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading (REFUND) - ";



	/**
	 * __construct
	 *
	 * Builds the initial XML document to add to for the request.
	 */
	public function __construct($base) {
		$this->base = $base;
		$this->xml = $this->base->xml;
	}


	/**
	 * build
	 *
	 * Build the main content into the Refund request XML.
	 * 
	 * @return string 	The XML to return.
	 */
	public function build() {

		$this->requestblock = $this->xml->dom->getElementsByTagName('requestblock')->item(0);

		//create the operation nodes
		$this->request = new DOMElement('request');
		$this->requestblock->appendChild($this->request);
		
		//get nodes
		$operation 	= $this->_getOperationNode();

		//set attributes of request node
		$this->request->setAttribute('type', $this->requestType);

		//add child elements of request
		$this->request->appendChild($operation);

		//save
		return $this->base;
	}


	/**
	 * _getOperationNode
	 *
	 * Creates the operation XML element with all its sub components.
	 * 
	 * @param  XMLHandler 	$this->xml    	XMLHandler class to create the elements within.
	 * @param  array     	$params 		The values we're populating the XML with.
	 * @return object 						The node to add to the XML.
	 */
	private function _getOperationNode() {

		//create the operation nodes
		$operation 			= new DOMElement('operation');
		$siteReference 		= new DOMElement('sitereference', $this->siteReference);
		$accountType		= new DOMElement('accounttypedescription', $this->accountType);

		$this->request->appendChild($operation);


		//add to billing element
		$operation->appendChild($siteReference);
		$operation->appendChild($accountType);

		//return operation
		return $operation;
	}


	/**
	 * validate
	 *
	 * Runs all validation methods.
	 */
	public function validate() {

		//run the validation methods
		$this->validateAccountType();
		$this->validateAmount();
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


	/**
	 * validateAmount
	 *
	 * Checks the amount is an int.
	 */
	private function validateAmount() {
		Validate::int($this->amount, $this->errorPrefix.'amount');
	}
}