<?php

/**
 * Notes:
 * 	- Only cares about Secure trading requests.
 * 	- Does not care about the request type, as long as its a secure trading one.
 */

namespace Dao\Payment\Gateways\SecureTrading;

use Exception;
use Dao\Payment\Gateways\Gateway;
use Dao\Payment\Helpers\Validate;

class SecureTrading extends Gateway {

	/**
	 * $requestNamespace
	 *
	 * The namespace that the request type classes reside under.
	 * 
	 * @var string
	 */
	protected $requestNamespace = 'Dao\\Payment\\Gateways\\SecureTrading\\Requests';


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading - ";


	/**
	 * $currencyCode
	 *
	 * The default currency code.
	 * 
	 * @var string
	 */
	protected $currencyCode = 'GBP';


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
	 * The version of the Secure trading API, being used.
	 * 
	 * @var string
	 */
	protected $apiVersion = '3.67';


	/**
	 * $alias
	 *
	 * The Secure Trading account alias.
	 */
	protected $alias;


	/**
	 * $accountType
	 *
	 * The default account type.
	 * 
	 * @var string
	 */
	protected $accountType = 'ECOM';


	/**
	 * $siteReference
	 *
	 * The site reference - alias will populate this, if left blank.
	 *  
	 * @var string
	 */
	protected $siteReference;


	/**
	 * $settings
	 *
	 * Settings for the gateway to be available on all requests.
	 * 
	 * @var array
	 */
	protected $settings = array();


	/**
	 * __construct
	 *
	 * Defines the default settings
	 */
	public function __construct() {

		//set defaults
		$this->settings = array(
			'apiIp'			=> $this->apiIp,
			'apiPort' 		=> $this->apiPort,
			'apiVersion' 	=> $this->apiVersion,
			'alias'			=> $this->alias,
			'accountType'	=> $this->accountType,
		);
	}


	/**
	 * request
	 *
	 * Pass in the default values, before calling the parent method, which handles all.
	 *
	 * The arrays are merged.
	 * 
	 * @param  string 	$type   	The request type to call.
	 * @param  array 	$options 	The options to pass to it.
	 */
	public function request($type, $options) {

		//validate properties
		$this->validate();

		//set the defaults here
		$defaults = array(
			'apiIp'				=> $this->apiIp,
			'apiPort'			=> $this->apiPort,
			'apiVersion'		=> $this->apiVersion,
			'alias'				=> $this->alias,
			'accountType' 		=> $this->accountType,
			'siteReference' 	=> $this->siteReference,
			'currencyCode'		=> $this->currencyCode,
		);

		//merge the options
		$options = array_merge($defaults, $options);

		//merge the options
		$options = array_merge($options, $this->settings);

		//set error code
		$errorCode = 0;

		do{
			//avoid the first loop, and only runs on 20004 - missing parent or 20005 - parent auth hasn't settled yet
			//ST documentation recommends calling again in a few mins on 20004 error, 10 seconds seems to work
			if($errorCode == '20004'|| $errorCode == '20005'){
			
				//wait before trying again
				sleep(10);
			}

			//call parent method with the merged options
			$result = Parent::request($type, $options);

			//get error
			$error = $result->error();

			//if theres an error, set the code
			if($error){
				$errorCode = $error->code();
			}
		}
		while($errorCode == '20004' || $errorCode == '20005');

		//return result
		return $result;
	}


	/**
	 * validate
	 * 
	 * Run all validation methods.
	 */
	public function validate() {

		//call validate methods
		$this->validateApiIp();
		$this->validateApiPort();
		$this->validateApiVersion();
		$this->validateAlias();
		$this->validateAccountType();
		$this->validateSiteReference();
		$this->validateCurrencyCode();
	}


	/**
	 * validateApiIp
	 *
	 * Make sure the API IP address is a string.
	 */
	private function validateApiIp() {

		//make sure it's a string
		Validate::string($this->settings['apiIp'], $this->errorPrefix.'API IP');
		Validate::ip($this->settings['apiIp'], $this->errorPrefix.'API IP');
	}


	/**
	 * validateApiPort
	 *
	 * Make sure the port is an integer.
	 */
	private function validateApiPort() {

		//make sure it's a string
		Validate::int($this->settings['apiPort'], $this->errorPrefix.'API Port');
	}


	/**
	 * validateApiVersion
	 *
	 * Api version must be a string.
	 */
	private function validateApiVersion() {

		//make sure it's a string
		Validate::string($this->settings['apiVersion'], $this->errorPrefix.'api version');
	}


	/**
	 * validateAlias
	 *
	 * Alias must be a string.
	 */
	private function validateAlias() {

		//make sure it's a string
		Validate::string($this->settings['alias'], $this->errorPrefix.'alias');
	}


	/**
	 * validateAccountType
	 *
	 * Makes sure the account type if of the types in the allowed array.
	 */
	private function validateAccountType() {

		//set allowed values
		$allowed = array(
			'ECOM', 'MOTO', 'RECUR'
		);

		//make sure it's a string
		Validate::string($this->accountType, $this->errorPrefix.'account type');

		//make sure it's allowed
		if(!in_array($this->accountType, $allowed)) {
			throw new Exception($errorPrefix."account type is not valid.");
		}
	}


	/**
	 * validateSiteReference
	 *
	 * If no site reference is set, use the alias. Either way, it must be a string.
	 */
	private function validateSiteReference() {

		//ensure site reference is set - can be the alias if not defined
		$this->settings['siteReference'] = (!isset($this->settings['siteReference']))? $this->settings['alias'] : $this->settings['siteReference'];

		//make sure it's a string
		Validate::string($this->settings['siteReference'], $this->errorPrefix.'site reference');
	}


	/**
	 * validateCurrencyCode
	 *
	 * Currency code must be one of the allowed options. It must be a string.
	 */
	private function validateCurrencyCode() {

		//set allowed values
		$allowed = array(
			'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'ATS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BEF', 'BGL', 'BGN', 'BHD', 'BIF', 'BMD',
			'BND', 'BOB', 'BRL', 'BSD', 'BTN', 'BWP', 'BYR', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CUP', 'CVE', 'CYP', 'CZK', 'DEM',
			'DJF', 'DKK', 'DOP', 'DZD', 'ECS', 'EEK', 'EGP', 'ERN', 'ESP', 'ETB', 'EUR', 'FIM', 'FJD', 'FKP', 'FRF', 'GBP', 'GEL', 'GHS', 'GIP', 'GMD',
			'GNF', 'GRD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'IEP', 'ILS', 'INR', 'IQD', 'IRR', 'ISK', 'ITL', 'JMD', 'JOD', 'JPY',
			'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'LTL', 'LUF', 'LVL', 'LYD', 'MAD', 'MDL',
			'MGA', 'MGF', 'MKD', 'MMK', 'MNT', 'MOP', 'MRO', 'MTL', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NLG', 'NOK', 'NPR',
			'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'PTE', 'PYG', 'QAR', 'ROL', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SDG',
			'SEK', 'SGD', 'SHP', 'SIT', 'SKK', 'SLL', 'SOS', 'SRD', 'SRG', 'SSP', 'STD', 'SVC', 'SYP', 'SZL', 'THB', 'TJS', 'TMT', 'TND', 'TOP', 'TRL',
			'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VEB', 'VEF', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR',
			'ZMK', 'ZWL'
		);

		//make sure it's a string
		Validate::string($this->currencyCode, $this->errorPrefix.'currency code');

		//make sure it's allowed
		if(!in_array($this->currencyCode, $allowed)) {
			throw new Exception($errorPrefix."currency code is not valid.");
		}
	}
}