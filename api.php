<?php 
//Authentication
	$user = 'seller_api1.awesome.com';
	$password = '1374512372';
	$signature = 'Aj1PRxuNKRh0FhwjgrTLGnn515trAGwGZHW7KLOlOuyQom-IEXlq.w4w';
	$appid = 'APP-80W284485P519543T';
	$params = array(
	
				'actionType' => strtoupper($_POST['action']),
				'returnUrl' => 'http://localhost/PayPalAdaptivePayments/return.php',
				'cancelUrl' => 'http://localhost/PayPalAdaptivePayments/cancel.php',
				'feesPayer' => 'EACHRECEIVER',
				'currencyCode' => 'USD',
				//'senderEmail' => 'buyer@awesome.com',
				'requestEnvelope.errorLanguage' => 'en_US '
				//'errorLang' => 'en_US',
				//'METHOD'	=> 'PARALLEL',
				//'RECEIVERS' => count($_POST['receiver'])
	
	);
	
	//toss in receivers and amounts
	foreach($_POST['receiver'] as $key => $value)
	{
		if(!$value)
			continue;
		$params['receiverList.receiver('.$key.').email'] = $value;
		$params['receiverList.receiver('.$key.').amount'] = $_POST['amount'][$key];
	}
	
	//print_r($params);
	//exit;
	

	//In PHP CURL will only accept a string for parameters so I am going to turn my array into a query string
	//DO NOT use http_build_query or any sort of URL encoding on the parameters.
	//Just take the key and value
	$querystring = '';
	foreach($params as $key => $value)
		$querystring .= $key . '=' . $value . '&';

 	//Setup URLS
    $url = 'https://svcs.paypal.com/AdaptivePayments/';
    $url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/'; //COMMENT THIS LINE OUT FOR a LIVE TRANSACTION
    
    $url .= $_POST['action'];
    
    //Setup CURL CALL to get a secure token for the transaction
    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt ($ch, CURLOPT_POST, true);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $querystring);  //Set My query string
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    		'X-PAYPAL-SECURITY-USERID: '.$user,
    		'X-PAYPAL-SECURITY-PASSWORD: '.$password,
    		'X-PAYPAL-SECURITY-SIGNATURE: '.$signature,
    		'X-PAYPAL-APPLICATION-ID: '.$appid,
    		'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
    		'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
    ));
   
    $response = curl_exec($ch);                //Execute the API Call
    print_r($response);        //Uncomment to view the raw response
    //echo $querystring;exit;
    //var_dump($ch);
    $key = explode('&',$response);
    foreach($key as $temp)
    {
    	$keyval = explode('=',$temp);
    	if(isset($keyval[1]))
    		$data[$keyval[0]] = $keyval[1];
    }
?>
<a href="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey=<?php echo $data['payKey']?>">Pay With PayPal</a>