<?php

/**
 * Notes:
 * 	- Does not care what type of request has been made.
 * 	- Only cares about Secure trading requests.
 */

namespace DannyAllen\Payment\Gateways\SecureTrading\Requests;

use Exception;

class Request{



	// public function build() {



	// 	//encode characters
	// 	$xml_string = $xml->dom->saveXML();

	// 	//return the dom!
	// 	return $xml_string."

	// 	";
	// }

	public function make($request) {
		die('got here');

		// open socket and send request.
		
		/* Create a TCP/IP socket. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
		    throw new Exception("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
		}
		
		// This is from the class settings
		$address = $this->api_ip;
		$service_port = $this->api_port;
		
		// echo "Attempting to connect to '$address' on port '$service_port'...";
		$result = @socket_connect($socket, $address, $service_port);
		if ($result === false) {
		     throw new Exception("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)));
		}
		
		$data = '';
		
		socket_write($socket, $request, strlen($request));
		
		while ($out = socket_read($socket, 2048)) {
		    $data .= $out;
		}
		socket_close($socket);
		
		return $data;
	}
	
}