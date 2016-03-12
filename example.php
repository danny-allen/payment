<?php
	
	//autoloader
	require_once('vendor/autoload.php');

	//what we are using
	use Dao\Payment;

	try {

		//set up new payment with gateway
		$payment = new Payment('SecureTrading');

		//configure
		$payment->setting('alias', 'some-alias');

		//prepare request, pass in callback to recieve the response.
		$response = $payment->request('Auth', array(
			'amount'					=> 100,
			'cardExpiry'				=> '05/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
			'cardType'					=> 'VISA'
		));

		// //we want an immediate refund on the payment
		// $payment->request('Refund', array(
		// 	'amount' => 100
		// ));

		//make request
		echo $response = $payment->make();

	} catch(Exception $e){

		//output error
		echo $e->getMessage();
	}