<?php
	
	//autoloader
	require_once('vendor/autoload.php');

	//what we are using
	use Dao\Payment;

	try {

		//set up new payment with gateway
		$payment = new Payment('SecureTrading');

		//work in test mode
		$payment->test = true;

		// //test condition
		// $payment->testConditions(array(
		// 	'success' => true
		// ));

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