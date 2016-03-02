<?php

/**
 * Notes:
 * 	- Only cares about the Auth type request for SecureTrading
 */

namespace DannyAllen\Payment\Gateways\SecureTrading\Requests;

use Exception;
use DOMDocument;
use XML\XMLHandler;
use DannyAllen\Payment\Gateways\SecureTrading\Requests\Request;

class Auth extends Request {

	public function __construct() {
		//$this->xml = new XMLHandler();
	}

	public function bluePrint() {
		return array(
			'alias'						=> 'string',
			'requestType'				=> 'AUTH',
			'siteReference'				=> $params['alias'],
			'accountTypeDescription'	=> $this->accountType,
			'currencyCode'				=> $this->currencyCode,
			'amount'					=> '1',
			'cardExpiry'				=> '01/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
		);
	}

	public function options($params) {
		$this->params = $params;
		$bluePrint = $this->bluePrint();
		Validate::Request($bluePrint, $this->params);
	}

	public function build($xml) {

		//make the XML object
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->saveXML($xml);

		//make the XML object
		$xml = new XMLHandler($dom);

		//requestblock attributes
		$requestblock 	= $xml->dom->createElement('requestblock');
		$requestblock->setAttribute('version', $this->api_version);

		//alias node
		$alias = $xml->dom->createElement('alias', $params['alias']);
		
		//get nodes
		$operation 	= $this->_getOperationNode($xml, $params);
		$billing 	= $this->_getBillingNode($xml, $params);
		$request 	= $xml->dom->createElement('request');

		//set attributes of request node
		$request->setAttribute('type', $params['requesttype']);

		//add child elements of request
		$xml->createChildElements($request, array(
			'operation' => $operation,
			'billing'	=> $billing
		));

		//create request block with children
		$xml->createChildElements($requestblock, array(
			'alias' 	=> $alias,
			'request' 	=> $request
		));

		//add the request block to the dom object
		$xml->createChildElements($dom, array(
			'requestblock' => $requestblock
		));
	}

	public function setup(){

		$defaults = array(
			'alias'						=> 'testsite',
			'requestType'				=> 'AUTH',
			'siteReference'				=> $params['alias'],
			'accountTypeDescription'	=> $this->accountType,
			'currencyCode'				=> $this->currencyCode,
			'amount'					=> '1',
			'cardExpiry'				=> '01/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
		);

		//merge the array
		$details = array_merge($defaults, $details);
	}
}