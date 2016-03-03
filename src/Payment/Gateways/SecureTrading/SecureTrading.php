<?php

/**
 * Notes:
 * 	- Only cares about Secure trading requests.
 * 	- Does not care about the request type, as long as its a secure trading one.
 */

namespace DannyAllen\Payment\Gateways\SecureTrading;

use Exception;
use DannyAllen\Payment\Gateways\Gateway;
use DannyAllen\Payment\Helpers\Validate;

class SecureTrading extends Gateway{

	/**
	 * $requestNamespace
	 *
	 * The namespace that the request type classes reside under.
	 * 
	 * @var string
	 */
	protected $requestNamespace = 'DannyAllen\\Payment\\Gateways\\SecureTrading\\Requests';


	/**
	 * $errorPrefix
	 * 
	 * Prefix for error messages.
	 * 
	 * @var string
	 */
	protected $errorPrefix = "Error: SecureTrading - ";


	/**
	 * $accountType
	 *
	 * The default account type.
	 * 
	 * @var string
	 */
	protected $accountType = 'ECOM';


	/**
	 * $currencyCode
	 *
	 * The default currency code.
	 * 
	 * @var string
	 */
	protected $currencyCode = 'GBP';


	/**
	 * $alias
	 * @var null
	 */
	protected $alias = null;


	protected $apiVersion = '3.67';


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

		//set the defaults here
		$defaults = array(
			'accountType' 	=> $this->accountType,
			'currencyCode'	=> $this->currencyCode,
			'apiVersion'	=> $this->apiVersion,
		);

		//merge the options
		$options = array_merge($defaults, $options);

		//validation here
		$this->validateAlias();
		$this->validateAccountType();
		$this->validateApiVersion();
		$this->validateSiteReference();
		$this->validateCurrencyCode();

		//call parent method with the merged options
		Parent::request($type, $options);
	}


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


	private function validateAlias() {

		//make sure it's a string
		Validate::string($this->alias, $this->errorPrefix.'alias');
	}


	private function validateApiVersion() {

		//make sure it's a string
		Validate::string($this->alias, $this->errorPrefix.'api version');
	}

	private function validateSiteReference() {

		//ensure site reference is set - can be the alias if not defined
		$this->siteReference = (!isset($this->siteReference))? $this->alias : $this->siteReference;

		//make sure it's a string
		Validate::string($this->siteReference, $this->errorPrefix.'site reference');
	}

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