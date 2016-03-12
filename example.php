<?php
	
	//autoloader
	require_once('vendor/autoload.php');

	//what we are using
	use Dao\Payment;

	try {

		//set up new payment with gateway
		$payment = new Payment('SecureTrading');

		//debug mode
		//$payment->debug = true;

		//configure
		$payment->setting('alias', 'test_royalhotel36271');

		//prepare request, pass in callback to recieve the response.
		$payment->request('Auth', array(
			'amount'					=> 100,
			'cardExpiry'				=> '05/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
			'cardType'					=> 'VISA'
		));

		//we want an immediate refund on the payment
		$payment->request('Refund', array(
			'amount' => 100
		));

		//make request
		echo $response = $payment->make();

		//close connection - important!
		$payment->done();

	} catch(Exception $e){

		//output error
		echo $e->getMessage();
	}