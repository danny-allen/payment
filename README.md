# payment

Here is an example of how to make a payment and immediatly perform a refund:

```php		
//autoloader
require_once('vendor/autoload.php');

//what we are using
use Dao\Payment;

try {

	//set up new payment with gateway
	$payment = new Payment('SecureTrading');

	//configure
	$payment->setting('alias', 'test_alias');

	//prepare request, pass in callback to recieve the response.
	$auth = $payment->request('Auth', array(
		'amount'					=> 100,
		'cardExpiry'				=> '05/16',
		'cardPan'					=> '4111111111111111',
		'cardSecurityCode'			=> '123',
		'cardType'					=> 'VISA'
	));

	//get transaction reference
	$transactionReference = $auth->transactionReference();

	//find errors
	$error = $auth->error();

	//check for error
	if($error){
		throw new Exception('Error Code: ' . $error->code() . " - " . $error->message());
	}

	//we want an immediate refund on the payment
	$refund = $payment->request('Refund', array(
		'amount' => 100,
		'parenttransactionreference' => $transactionReference
	));

	//get transaction reference
	$transactionReference = $refund->transactionReference();

	//find errors
	$error = $refund->error();

	//check for error
	if($error){
		throw new Exception('Error Code: ' . $error->code() . " - " . $error->message());
	}

} catch(Exception $e){

		//output error
		echo $e->getMessage();
}
```