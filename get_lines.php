<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once 'ptv_utils.php';
$mode = $_GET['mode'];
$linesByModeUrl = "/v2/lines/mode/" . $mode;

//get the signed URL based on Maggs St 906
$signedUrl = generateURLWithDevIDAndKey($linesByModeUrl);

//get the plain json response
$jsonStr = getJSONStr($signedUrl);

if (strpos($jsonStr, 'CURL_ERROR') !== false) {
	echo "{\"error_message\":\"$jsonStr\"}";
}else{
	echo $jsonStr;
}
?>