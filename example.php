<?php
	
	//autoloader
	require_once('vendor/autoload.php');

	//what we are using
	use Dao\Payment;

	try {

		//set up new payment with gateway
		$payment = new Payment('SecureTrading');

		//$payment->debug = true;

		//configure
		$payment->setting('alias', 'test_royalhotel36271');

		//prepare request, pass in callback to recieve the response.
		$auth = $payment->request('Auth', array(
			'amount'					=> 100,
			'cardExpiry'				=> '05/16',
			'cardPan'					=> '4111111111111111',
			'cardSecurityCode'			=> '123',
			'cardType'					=> 'VISA'
		));

		//get transaction reference
		echo $transactionReference = $auth->transactionReference();

		//check for error
		if($error = $auth->error()){
			throw new Exception('Error Code: ' . $error->code() . " - " . $error->message());
		}

		//we want an immediate refund on the payment
		 
		$errorCode = 0;
		do{
			$refund = $payment->request('Refund', array(
				'amount' => 100,
				'parenttransactionreference' => $transactionReference
			));

			$error = $refund->error();
			$errorCode = $error->code();
			
			//wait before trying again
			sleep(10);
		}
		while($errorCode == '20004');

		//get transaction reference
		echo $transactionReference = $refund->transactionReference();

		//check for error
		if($error = $refund->error()){
			throw new Exception('Error Code: ' . $error->code() . " - " . $error->message());
		}

		echo $transactionReference;
		

	} catch(Exception $e){

		//output error
		echo $e->getMessage();
	}