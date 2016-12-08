<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once 'ptv_utils.php';
$mode = $_GET['mode'];
$stop = $_GET['stop'];
$generalurl = "/v2/mode/" . $mode . "/stop/" . $stop . "/departures/by-destination/limit/1";

//get the signed URL
$signedUrl = generateURLWithDevIDAndKey($generalurl);

//get the plain json response
$jsonStr = getJSONStr($signedUrl);

if (strpos($jsonStr, 'CURL_ERROR') !== false) {
	echo "{\"error_message\":\"$jsonStr\"}";
}else{
	echo $jsonStr;
}
?>
