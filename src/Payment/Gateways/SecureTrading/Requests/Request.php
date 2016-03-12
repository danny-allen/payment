<?php

/**
 * Notes:
 * 	- Does not care what type of request has been made.
 * 	- Only cares about Secure trading requests.
 */

namespace Dao\Payment\Gateways\SecureTrading\Requests;

use Exception;
use Dao\Plug;
use Dao\XMLHandler;
use Dao\Payment\Helpers\Validate;

class Request {


	/**
	 * $debug
	 *
	 * Whether or not we're in debug mode.
	 * 
	 * @var boolean
	 */
	public $debug = false;



	public function __construct($options) {
		$this->options = $options;
	}


	/**
	 * setHeaders
	 *
	 * The response may be in a few different formats depending on the gateway
	 * used (XML, JSON etc...).
	 * 
	 * @param  string 	$format   	The content type to use.
	 */
	protected function setHeaders($format) {

		//make sure format is a string
		Validate::string($format);

		//set header
		header("Content-type: ".$format);
	}


	/**
	 * make
	 *
	 * Make the request. Write the XML generated from the build function to the socket.
	 * 
	 * @return string  	The output from the socket, based on the data sent.
	 */
	public function make($xml) {

		//set xml
		$this->xml = $xml;

		//check deug
		if($this->debug){

			//we're expecting xml
			$this->setHeaders('text/xml');
			echo $this->xml;
			die();
		}

		//check for request XML
		if(!isset($this->xml)){
			throw new Exception($this->errorPrefix."requestXML is empty.");
		}

		//make sure the request XML is a string - it should be by this point.
		Validate::string($this->xml);

		//instantiate a plug - creates a socket.
		$this->plug = new Plug();

		//connect the plug.
		$this->plug->connect($this->options['apiIp'], $this->options['apiPort']);

		var_dump($this->xml);
		die('deadus');

		//switch it on and get the output.
		$output = $this->plug->on($this->xml);

		//switch it off, we're done.
		$this->plug->off();

		//we're expecting xml
		$this->setHeaders('text/xml');
		
		//return the response from the socket
		return $output;
	}
}