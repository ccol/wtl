<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once 'ptv_utils.php';
$minsAway = 7;
$specificurl = "/v2/mode/2/line/8596/stop/21301/directionid/40/departures/all/limit/3";

//get the signed URL based on Maggs St 906
$signedUrl = generateURLWithDevIDAndKey($specificurl);

//get the next departure time
$nextTime = getNextDepartureTime($signedUrl);

if (strpos($nextTime, 'CURL_ERROR') !== false) {
	echo "ERROR: $nextTime";
}else{
	//get the number of minutes till departure time
	$diffInMins = minutesTillDeparture($nextTime);
	//if departure time is 10 mins away, then do something
	//if($diffInMins === $minsAway)
	//	echo "<h1>It's Time!!!</h1>";

//just testing, so return something after sending a push
	$sendpush = sendPush($diffInMins);
	//SHOULD PUT A CHECK IN HERE TO CHECK $sendpush FOR AN ERROR MESSAGE, E.G. CHECK THAT SUCCESS:"1"
	echo "$diffInMins";
}
?>