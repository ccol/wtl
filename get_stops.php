<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once 'ptv_utils.php';
$mode = $_GET['mode'];
$line = $_GET['line'];
$stopsurl = "/v2/mode/" . $mode . "/line/" . $line . "/stops-for-line";

//get the signed URL
$signedUrl = generateURLWithDevIDAndKey($stopsurl);

//get the plain json response
$jsonStr = getJSONStr($signedUrl);

if (strpos($jsonStr, 'CURL_ERROR') !== false) {
	echo "{\"error_message\":\"$jsonStr\"}";
}else{
	echo $jsonStr;
}
?>