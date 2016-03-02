<?php

/**
 * Notes:
 * 	- Only cares about Secure trading requests.
 * 	- Does not care about the request type, as long as its a secure trading one.
 */

namespace DannyAllen\Payment\Gateways\SecureTrading;

use Exception;
use DannyAllen\Payment\Gateways\Gateway;

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

	//need our settings declared - vars or array?
	public $accountType = 'ECOM';
	public $currencyCode = 'GBP';
}