<?php

/**
 * Notes:
 * 	- Only cares about the Auth type request for SecureTrading
 */

namespace DannyAllen\Payment\Gateways\SecureTrading\Requests;

use Exception;
use DOMDocument;
use DateTime;
use DannyAllen\XMLHandler;
use DannyAllen\Payment\Helpers\Validate;
use DannyAllen\Payment\Gateways\SecureTrading\Requests\Request;

class Auth extends Request {

	protected $accountType;
	protected $currencyCode;
	protected $alias;
	protected $apiVersion;
	protected $requestType = 'AUTH';
	protected $siteReference;

	protected $amount = '0';
	protected $cardExpiry;
	protected $cardPan;
	protected $cardSecurityCode;
	protected $cardType;


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading (AUTH) - ";



	public function __construct() {

		//make the XML object
		$this->dom = new DOMDocument('1.0', 'utf-8');

		//make the XML object
		$this->xml = new XMLHandler($this->dom);
	}


	public function build() {

		//firstly, run the validation method and make sure we have everything.
		$this->validate();

		//requestblock attributes
		$requestblock 	= $this->xml->dom->createElement('requestblock');
		$requestblock->setAttribute('version', $this->apiVersion);

		//alias node
		$alias = $this->xml->dom->createElement('alias', $this->alias);
		
		//get nodes
		$operation 	= $this->_getOperationNode();
		$billing 	= $this->_getBillingNode();
		$request 	= $this->xml->dom->createElement('request');

		//set attributes of request node
		$request->setAttribute('type', $this->requestType);

		//add child elements of request
		$this->xml->createChildElements($request, array(
			'operation' => $operation,
			'billing'	=> $billing
		));

		//create request block with children
		$this->xml->createChildElements($requestblock, array(
			'alias' 	=> $alias,
			'request' 	=> $request
		));

		//add the request block to the dom object
		$this->xml->createChildElements($this->dom, array(
			'requestblock' => $requestblock
		));

		//store the request XML
		$this->requestXML = $this->xml->dom->saveXML();

		//return it.
		return $this->requestXML;
	}


	/**
	 * _getOperationNode
	 *
	 * Creates the operation XML element with all its sub components.
	 * 
	 * @param  XMLHandler 	$this->xml    	XMLHandler class to create the elements within.
	 * @param  array     	$params 	The values we're populating the XML with.
	 * 
	 * @return object 					The node to add to the XML.
	 */
	private function _getOperationNode() {

		//create the operation nodes
		$operation 				= $this->xml->dom->createElement('operation');
		$siteReference 			= $this->xml->dom->createElement('sitereference', $this->siteReference);
		$accountType	= $this->xml->dom->createElement('accounttypedescription', $this->accountType);

		//add to billing element
		$this->xml->createChildElements($operation, array(
			'sitereference' 			=> $siteReference,
			'accounttype' 	=> $accountType
		));

		//return operation
		return $operation;
	}


	/**
	 * _getBillingNode description
	 *
	 * Creates the billing XML element with all its sub components.
	 * 
	 * @param  XMLHandler 	$this->xml    	XMLHandler class to create the elements within.
	 * @param  array     	$params 	The values we're populating the XML with.
	 * 
	 * @return object 					The node to add to the XML.
	 */
	private function _getBillingNode() {
		
		//create the billing nodes
		$billing 	= $this->xml->dom->createElement('billing');
		$amount 	= $this->xml->dom->createElement('amount', $this->amount);
		$payment 	= $this->xml->dom->createElement('payment');


		//validate and set card attribute
		if(!isset($this->cardExpiry)){
			throw new Exception("Card date not valid.");
		}

		if(!isset($this->cardSecurityCode)){
			throw new Exception("Card security code not valid.");	
		}

		//create child elements of payment node
		$this->xml->createChildElements($payment, array(
			'expirydate' 	=> (string) $this->cardExpiry,
			'pan'			=> $this->cardPan,
			'securitycode'	=> $this->cardSecurityCode,
		));

		//set attributes on currency code
		$payment->setAttribute('type', 'GBP');

		//set attributes on currency code
		$amount->setAttribute('currencycode', 'GBP');

		//validate and set card attribute
		// if(!isset($this->cardType) || !$this->validatePaymenttype($this->cardType)){
		// 	throw new Exception("Card type not valid.");
		// }

		$payment->setAttribute('type', $this->cardType);

		//add to billing element
		$this->xml->createChildElements($billing, array(
			'amount' 	=> $amount,
			'payment' 	=> $payment
		));

		//return billing element
		return $billing;
	}

	public function validate() {

		//run the validation methods
		$this->validateAccountType();
		$this->validateAmount();
		$this->validateCardExpiry();
		$this->validateCardPan();
		$this->validateCardSecurityCode();
		$this->validateCardType();

	}



	/**
	 * validateAccountType
	 *
	 * 
	 * 
	 * @return [type] [description]
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
	 *
	 * 
	 * @return [type] [description]
	 */
	private function validateAmount() {
		Validate::int($this->amount, $this->errorPrefix.'amount');
	}


	/**
	 * validateAmount
	 * 
	 *
	 * 
	 * @return [type] [description]
	 */
	private function validateCardExpiry() {

		//check it's a string
		Validate::string($this->cardExpiry, $this->errorPrefix.'card expiry');
		
		//get expiry date and current date
		$expires = DateTime::createFromFormat('m/y', $this->cardExpiry);

		//now date time
		$now = new DateTime();

		//compare the dates
		if ($expires < $now) {
			throw new Exception($this->errorPrefix."card has expired or expiry date is invalid.");
		}

		//card expiry format
		$this->cardExpiry = $expires->format('m/Y');
	}


	/**
	 * validateAmount
	 * 
	 *
	 * 
	 * @return [type] [description]
	 */
	private function validateCardPan() {

		//check it's a string
		Validate::string($this->cardPan, $this->errorPrefix.'card number');
	}


	/**
	 * validateAmount
	 * 
	 *
	 * 
	 * @return [type] [description]
	 */
	private function validateCardSecurityCode() {

		//check it's a string
		Validate::string($this->cardSecurityCode, $this->errorPrefix.'card security code');

	}


	/**
	 * validateAmount
	 *
	 * 
	 * 
	 * @return [type] [description]
	 */
	private function validateCardType() {

		//allowed card types
		$allowed = array(
			'VISA',
			'DELTA',
			'PURCHASING',
			'ELECTRON',
			'MAESTRO',
			'MASTERCARD',
			'MASTERCARDDEBIT',
		);

		//make sure it's a string
		Validate::string($this->cardType, $this->errorPrefix.'card type');

		//make sure it's allowed
		if(!in_array($this->cardType, $allowed)) {
			throw new Exception($errorPrefix."card type is not valid.");
		}
	}
}