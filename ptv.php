<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

$key = "22d89862-7652-11e6-a0ce-06f54b901f07"; // supplied by PTV
$developerId = 1000857; // supplied by PTV
$date = gmdate('Y-m-d\TH:i:s\Z');
$healthcheckurl = "/v2/healthcheck?timestamp=" . $date;
$nearmeurl = "/v2/nearme/latitude/-37.7989769/longitude/144.919174";
$stopsurl = "/v2/mode/2/line/8596/stops-for-line";
$generalurl = "/v2/mode/2/stop/21301/departures/by-destination/limit/1";
$specificurl = "/v2/mode/2/line/8596/stop/21301/directionid/40/departures/all/limit/3";
$linesByModeUrl = "/v2/lines/mode/2";
?>
<h1>Health Check</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($healthcheckurl, $developerId, $key);
drawResponse($signedUrl);
?>
<h1>Near Me</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($nearmeurl, $developerId, $key);
drawResponse($signedUrl);
?>
<h1>Lines by Mode (2 for bus)</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($linesByModeUrl, $developerId, $key);
drawResponse($signedUrl);
?>
<h1>Stops for Line</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($stopsurl, $developerId, $key);
drawResponse($signedUrl);
?>
<h1>General Next Departures</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($generalurl, $developerId, $key);
drawResponse($signedUrl);
?>
<h1>Specific Next Departures</h1>
<? 
$signedUrl = generateURLWithDevIDAndKey($specificurl, $developerId, $key);
drawResponse($signedUrl);
function generateURLWithDevIDAndKey($apiEndpoint, $developerId, $key)
{
	// append developer ID to API endpoint URL
	if (strpos($apiEndpoint, '?') > 0)
	{
		$apiEndpoint .= "&";
	}
	else
	{
		$apiEndpoint .= "?";
	}
	$apiEndpoint .= "devid=" . $developerId;
 
	// hash the endpoint URL
	$signature = strtoupper(hash_hmac("sha1", $apiEndpoint, $key, false));
 
	// add API endpoint, base URL and signature together
	return "http://timetableapi.ptv.vic.gov.au" . $apiEndpoint . "&signature=" . $signature;
}
function drawResponse($signedUrl)
{
    echo "<p>$signedUrl</p>";
    echo "<textarea rows=\"10\" cols=\"60\">";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $signedUrl); 
    curl_setopt($ch, CURLOPT_TIMEOUT, '3'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    echo $xmlstr = curl_exec($ch); 
    curl_close($ch);
    
    echo "</textarea>";
}
?>