<?php
	
	//autoloader
	require_once('vendor/autoload.php');

	//what we are using
	use DannyAllen\Payment;

	try {

		//set up new payment with gateway
		$payment = new Payment('SecureTrading');

		//configure
		$payment->setting('alias', 'some-alias');

		//options
		$options = array(
			'amount'					=> 100,
			'cardExpiry'				=> '05/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
			'cardType'					=> 'VISA'
		);

		//make request, pass in callback to recieve the response.
		$payment->request('Auth', $options, function($response){
			echo $response;
		});

	} catch(Exception $e){

		//output error
		echo $e->getMessage();
	}