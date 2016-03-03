<?php

/**
 * Notes:
 * 	- Does not care what type of request has been made.
 * 	- Only cares about Secure trading requests.
 */

namespace DannyAllen\Payment\Gateways\SecureTrading\Requests;

use Exception;
use DannyAllen\Plug;
use DannyAllen\Payment\Helpers\Validate;

class Request{


	protected $errorPrefix = "Error: SecureTrading - ";
	protected $requestXML;


	public function make() {

		//check for request XML
		if(!isset($this->requestXML)){
			throw new Exception($this->errorPrefix."requestXML is empty.");
		}

		//make sure the request XML is a string - it should be by this point.
		Validate::string($this->requestXML);



		//instantiate a plug - creates a socket.
		$plug = new Plug();

		//connect the plug.
		$plug->connect('127.0.0.1', 5000);

		//switch it on and get the output.
		$output = $plug->on($this->requestXML);

		//switch it off, we're done.
		$plug->off();



		die('got here - make');
		// open socket and send request.
		
		// This is from the class settings
		$address = $this->api_ip;
		$service_port = $this->api_port;
		
		$data = '';
		
		socket_write($socket, $request, strlen($request));
		
		while ($out = socket_read($socket, 2048)) {
		    $data .= $out;
		}
		socket_close($socket);
		
		return $data;
	}


	private function socketConnect() {
		
		// echo "Attempting to connect to '$address' on port '$service_port'...";
		$result = @socket_connect($socket, $address, $service_port);
		if ($result === false) {
		     throw new Exception("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));
		}

	}


	private function socketCreate() {
		
		/* Create a TCP/IP socket. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		if ($socket === false) {
		    throw new Exception("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
		}
	}






	public function options($options) {

		foreach($options as $option => $value){

			//get the child class, to check properties against
			$class = get_class($this);

			//check option is a string
			Validate::string($option);
			
			//check property value
			if(property_exists($class, $option)){
				$this->{$option} = $value;
			}else{
				throw new Exception('The '.(string) $option.' property does not exist.');
			}
		}

		//$this->validateOptions();
	}


	
}