<?php

/**
 * Notes:
 * 	- Only cares about the Auth type request for SecureTrading
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests\Auth;

use Exception;
use DOMDocument;
use DateTime;
use Dao\XMLHandler;
use Dao\Payment\Helpers\Validate;
use Dao\Payment\Gateways\SecureTrading\Requests\Request;

class Auth extends Request {


	/**
	 * $requestType
	 *
	 * The type of request to be made to Secure Trading.
	 * 
	 * @var string
	 */
	protected $requestType = 'AUTH';

	/**
	 * $amount
	 *
	 * The amount to be used in the transaction. It's passed in pence.
	 * 
	 * @var int
	 */
	protected $amount = 0;


	/**
	 * $cardExpiry
	 *
	 * The expiry date of the card expected as mm/yy.
	 * 
	 * @var string
	 */
	protected $cardExpiry;


	/**
	 * $cardPan
	 *
	 * The long card number expected as a string.
	 * 
	 * @var string
	 */
	protected $cardPan;


	/**
	 * $cardSecurityCode
	 *
	 * The 3/4 digit security code on the back of the card, expected as a string.
	 * 
	 * @var string
	 */
	protected $cardSecurityCode;


	/**
	 * $cardType
	 *
	 * The type of card. e.g VISA.
	 * 
	 * @var string
	 */
	protected $cardType;


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading (AUTH) - ";



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
	 * build
	 *
	 * Build the main content into the Auth request XML.
	 * 
	 * @return string 	The XML to return.
	 */
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
	 * @param  array     	$params 		The values we're populating the XML with.
	 * @return object 						The node to add to the XML.
	 */
	private function _getOperationNode() {

		//create the operation nodes
		$operation 				= $this->xml->dom->createElement('operation');
		$siteReference 			= $this->xml->dom->createElement('sitereference', $this->siteReference);
		$accountType			= $this->xml->dom->createElement('accounttypedescription', $this->accountType);

		//add to billing element
		$this->xml->createChildElements($operation, array(
			'sitereference' 			=> $siteReference,
			'accounttype' 	=> $accountType
		));

		//return operation
		return $operation;
	}


	/**
	 * _getBillingNode
	 *
	 * Creates the billing XML element with all its sub components.
	 * 
	 * @return object 	The node to add to the XML.
	 */
	private function _getBillingNode() {
		
		//create the billing nodes
		$billing 	= $this->xml->dom->createElement('billing');
		$amount 	= $this->xml->dom->createElement('amount', $this->amount);
		$payment 	= $this->xml->dom->createElement('payment');

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

		$payment->setAttribute('type', $this->cardType);

		//add to billing element
		$this->xml->createChildElements($billing, array(
			'amount' 	=> $amount,
			'payment' 	=> $payment
		));

		//return billing element
		return $billing;
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
		$this->validateCardExpiry();
		$this->validateCardPan();
		$this->validateCardSecurityCode();
		$this->validateCardType();
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


	/**
	 * validateCardExpiry
	 * 
	 * Checks the card expiry is in the future and is valid.
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
	 * validateCardPan
	 *
	 * Checks the long card number is a string.
	 */
	private function validateCardPan() {

		//check it's a string
		Validate::string($this->cardPan, $this->errorPrefix.'card number');
	}


	/**
	 * validateCardSecurityCode
	 *
	 * Checks the security code is a string.
	 */
	private function validateCardSecurityCode() {

		//check it's a string
		Validate::string($this->cardSecurityCode, $this->errorPrefix.'card security code');

	}


	/**
	 * validateCardType
	 *
	 * Checks the card type matches one of the allowed values, and that its a string.
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