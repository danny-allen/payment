<?php

/**
 * Notes:
 * 	- Only cares about the Auth type request for SecureTrading
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests\Auth;

use Exception;
use DOMDocument;
use DOMElement;
use DateTime;
use Dao\XMLHandler;
use Dao\Payment\Helpers\Validate;
use Dao\Payment\Gateways\SecureTrading\Requests\Base;

class Auth extends Base {


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
	public function __construct($base) {
		$this->base = $base;
		$this->xml = $this->base->xml;
	}


	/**
	 * build
	 *
	 * Build the main content into the Auth request XML.
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
		$billing 	= $this->_getBillingNode();

		//set attributes of request node
		$this->request->setAttribute('type', $this->requestType);

		//add child elements of request
		$this->request->appendChild($operation);
		$this->request->appendChild($billing);

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
		$operation 				= new DOMElement('operation');
		$siteReference 			= new DOMElement('sitereference', $this->siteReference);
		$accountType			= new DOMElement('accounttypedescription', $this->accountType);

		
		$this->requestblock->appendChild($operation);

		//add child elements of request
		$operation->appendChild($siteReference);
		$operation->appendChild($accountType);

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
		$billing 	= new DOMElement('billing');
		$amount 	= new DOMElement('amount', $this->amount);
		$payment 	= new DOMElement('payment');

		
		$this->requestblock->appendChild($billing);

		//append to billing
		$billing->appendChild($payment);
		$billing->appendChild($amount);

		//create the billing nodes
		$expirydate 	= new DOMElement('expirydate', $this->cardExpiry);
		$pan 		 	= new DOMElement('pan', $this->cardPan);
		$securitycode	= new DOMElement('securitycode', $this->cardSecurityCode);

		//create child elements of payment node
		$payment->appendChild($expirydate);
		$payment->appendChild($pan);
		$payment->appendChild($securitycode);

		//set payment attributes on currency code
		$payment->setAttribute('currency', 'GBP');
		$payment->setAttribute('type', $this->cardType);

		//set attributes on currency code
		$amount->setAttribute('currencycode', 'GBP');

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

		$this->cardExpiry;

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