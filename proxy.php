<?php 
$adaptive =  json_decode(file_get_contents("php://input"));

$url = $adaptive->urlbase . $adaptive->urlparams;

//Make Headers
$headers = array();
foreach($adaptive->headers as $key => $value)
	$headers[] = $key .': ' . $value;

$ch = curl_init ();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $adaptive->apicall);  //Set My query string
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 
$response = curl_exec($ch);                //Execute the API Call
$key = explode('&',$response);
foreach($key as $temp)
{
	$keyval = explode('=',$temp);
    if(isset($keyval[1]))
    	$data[$keyval[0]] = $keyval[1];
}

echo json_encode($data);
?>