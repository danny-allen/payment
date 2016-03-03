<?php
	
	require_once('vendor/autoload.php');

	use DannyAllen\Payment;

	//set params
	// $params['alias'] 					= 'some-alias';
	// $params['requesttype']				= 'AUTH';
	// $params['sitereference']			= $params['alias'];
	// $params['accounttypedescription']	= 'ECOM';
	// $params['currencycode']				= 'GBP';
	// $params['amount']					= '1';

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

		//request AUTH type transaction with options
		if(!$payment->request('Auth', $options)){
			throw new Exception($payment->status());
		}

	} catch(Exception $e){

		//output error
		echo $e->getMessage();
	}
