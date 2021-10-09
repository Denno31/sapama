<?php
require_once 'nusoap.php';
//Set the api_key, api_secret and endpoint as configs in the system that can be setup during configuration
function get_transactions($api_key,$api_secret){

//$api_key = 'key';
//$api_secret = 'secret';
$endpoint = 'https://sapamacash.com/api/get_transactions';

//Data to send a query string
$data = array(
    'format' => 'json',
    'per_page' => 2,
    'page' => 1,
    'api_key' => $api_key,
    'api_secret' => $api_secret,
        //'phone'=>'254722906835' - Optional //INCLUDE 254 without the +
        //'trans_id'=>'' - Optional
);
//Sort by keys in ascending order
ksort($data);

//Implode the string
$string_to_hash = implode($data, '.');

//echo 'String to hash: ' . $string_to_hash . '<p>';

//Generate hash
$hash = hash('sha256', $string_to_hash, false);

//echo 'Generated hash: ' . $hash . '<p>';

//IMPORTANT: REMEMBER TO ADD THE GENERATED HASH TO TO THE DATA
$data['hash'] = $hash;

//IMPORTANT: REMEMBER TO REMOVE THE API SECRET FROM THE DATA HASHED
unset($data['api_secret']);
//var_dump($data);

$fields_string = '';
foreach ($data as $key => $value) {
    $fields_string .= $key . '=' . $value . '&';
}//E# foreach statement

rtrim($fields_string, '&');

//echo 'Query string: ' . $fields_string . '<p>';

//echo 'Full url: ' . $endpoint . '?' . $fields_string . '<p>';

// Get cURL resource
$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . $fields_string);

$result = curl_exec($ch);

curl_close($ch);

//echo 'Result in JSON<p>' . $result . '<p>';
//echo $result;
//return $result;// "1,2,3,4,5,6,7";
//exit();
$decoded_data = json_decode($result, true);
//echo 'Decoded array<p>';

//var_dump($decoded_data);
$myfile = fopen("whiterhinoerror.txt", "w");
fwrite($myfile, $decoded_data['httpStatusCode']);
fclose($myfile);
	
if ($decoded_data['httpStatusCode'] == 200 && array_key_exists('data', $decoded_data['data'])) {
    //echo '<p>Success<p>';
      $reply="Success";
    $index = 0;
    foreach ($decoded_data['data']['data'] as $single_transaction) {
        //echo "<p>Transaction " . $index . '<p>';
        //var_dump($single_transaction);
        $index++;
        $t_id=$single_transaction["id"];
        $orgId=$single_transaction["organization_id"];
        $trans_type=$single_transaction["trans_type"];
        $trans_id=$single_transaction["trans_id"];
        $trans_time=$single_transaction["trans_time"];
        $date=$single_transaction["date"];
        $trans_amount=$single_transaction["trans_amount"];
        $short_code=$single_transaction["short_code"];
        $bill_ref_number=$single_transaction["bill_ref_number"];
        $phone=$single_transaction["phone"];
        $name=$single_transaction["name"];
        $first_name=$single_transaction["first_name"];
        $last_name=$single_transaction["last_name"];
        //$name=$single_transaction["phone"];
        $transaction="|".$t_id."*".$trans_id."*".$date."*".$trans_time."*".$trans_amount."*".$phone."*".$first_name."*".$last_name;
        $reply=$reply.$transaction;

    }//E# foreach statement
      return $reply;
} else {
    $reply="Failed";
      return $reply;
    /*
    echo "<p>HTTP Status Code: " . $decoded_data['httpStatusCode'] . '<p>';
    echo "System Code: " . $decoded_data['systemCode'] . '<p>';
    echo "Message: " . $decoded_data['systemCode'] . '<p>'; */
}

}

function update_transaction($api_key,$api_secret,$transid){
$trans_ID=(int)str_replace(' ', '', $transid);
  /*$api_key = 'key';
  $api_secret = 'secret'; */
  $endpoint = 'http://sapamacash.com/api/update_transaction';

  //Data to send a query string
  $data = array(
      'format' => 'json',
      'id' => $trans_ID,
      'ipned' => 'success',
      'api_key' => $api_key,
      'api_secret' => $api_secret,
  );
  //Sort by keys in ascending order
  ksort($data);

  //Implode the string
  $string_to_hash = implode($data, '.');

//  echo 'String to hash: ' . $string_to_hash . '<p>';

  //Generate hash
  $hash = hash('sha256', $string_to_hash, false);

//  echo 'Generated hash: ' . $hash . '<p>';

  //IMPORTANT: REMEMBER TO ADD THE GENERATED HASH TO TO THE DATA
  $data['hash'] = $hash;

  //IMPORTANT: REMEMBER TO REMOVE THE API SECRET FROM THE DATA HASHED
  unset($data['api_secret']);
//  var_dump($data);

  $fields_string = '';
  foreach ($data as $key => $value) {
      $fields_string .= $key . '=' . $value . '&';
  }//E# foreach statement

  rtrim($fields_string, '&');

//  echo 'Query string: ' . $fields_string . '<p>';

  //echo 'Full url: ' . $endpoint . '?' . $fields_string . '<p>';

  // Get cURL resource
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  curl_setopt($ch, CURLOPT_URL, $endpoint);
  curl_setopt($ch, CURLOPT_POST, count($data));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

  $result = curl_exec($ch);
  //echo 'Result in JSON<p>' . $result . '<p>';

  $decoded_data = json_decode($result, true);
//  echo 'Decoded array<p>';

//  var_dump($decoded_data);

  if ($decoded_data['httpStatusCode'] == 200 && array_key_exists('data', $decoded_data['data'])) {
  //    echo '<p>Success<p>';
      $index = 0;
      foreach ($decoded_data['data']['data'] as $single_transaction) {
        //  echo "<p>Transaction " . $index . '<p>';
        //  var_dump($single_transaction);
        //  $index++;
           $reply="Success";
           return $reply;
      }//E# foreach statement
  } else {
      /*echo "<p>HTTP Status Code: " . $decoded_data['httpStatusCode'] . '<p>';
      echo "System Code: " . $decoded_data['systemCode'] . '<p>';
      echo "Message: " . $decoded_data['systemCode'] . '<p>';*/
      $reply="Failed";
      return $reply;

  }

}

$server =new soap_server();
$namespace = "http://sanity-free.org/services";
$server->configureWSDL("HotelPlusMpesaService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
                'get_transactions',
                array('apikey' => "xsd:string",'api_secret'=>"xsd:string"),
                array('mpesa_transactions'=>'xsd:string'),
                $namespace,
                false,
                'rpc',
                'encoded',
                'Get all Mpesa Transactions based on the Supplied Key and Secret word');
                $server->register(
                                'update_transaction',
                                array('apikey' => "xsd:string",'api_secret'=>"xsd:string","transid"=>"xsd:string"),
                                array('reply'=>'xsd:string'),
                                $namespace,
                                false,
                                'rpc',
                                'encoded',
                                'Flagoff downloaded transactions');
                $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
                                ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

                // pass our posted data (or nothing) to the soap service
                $server->service($POST_DATA);
                exit();
//$server->service($HTTP_RAW_POST_DATA);
