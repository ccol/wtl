<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

function generateURLWithDevIDAndKey($apiEndpoint){
	$key = "22d89862-7652-11e6-a0ce-06f54b901f07"; // supplied by PTV
	$developerId = 1000857; // supplied by PTV

	// append developer ID to API endpoint URL
	if (strpos($apiEndpoint, '?') > 0)
		$apiEndpoint .= "&";
	else
		$apiEndpoint .= "?";
	$apiEndpoint .= "devid=" . $developerId;
 
	// hash the endpoint URL
	$signature = strtoupper(hash_hmac("sha1", $apiEndpoint, $key, false));
 
	// add API endpoint, base URL and signature together
	return "http://timetableapi.ptv.vic.gov.au" . $apiEndpoint . "&signature=" . $signature;
}

function getJSONStr($signedUrl){
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $signedUrl); 
    curl_setopt($ch, CURLOPT_TIMEOUT, '3'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $returnStr = curl_exec($ch); 

	if (curl_errno($ch)) {
	    return 'CURL_ERROR:' . curl_error($ch);
	}
	curl_close ($ch);
    return $returnStr;
}

function getNextDepartureTime($signedUrl){
	$arrStr = getJSONStr($signedUrl);

	//check to see if there was an error
	if (strpos($arrStr, 'CURL_ERROR') !== false) {
		return "<p>PTV API failed with CURL_ERROR: $arrStr</p>";
	}else{ //success
		//convert to json object
		$arr = json_decode($arrStr);
		$nextTimetableDeparture = null;
		$nextRealtimeDeparture = null;
		foreach($arr->values as $item){
			$nextTimetableDeparture = $item->time_timetable_utc;
			$nextRealtimeDeparture = $item->time_realtime_utc;
			break;
		}
		if(is_null($nextRealtimeDeparture))
			return $nextTimetableDeparture;
		else
			return $nextRealtimeDeparture;
	}
}
function minutesTillDeparture($nextDepartureTime){
	date_default_timezone_set('Australia/Melbourne');
	$next = strtotime($nextDepartureTime);
	return intval(floor(($next-time()) / 60), 10);
}
function sendPush(){

	// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"registration_ids\":[\"d6G-O4CcuAU:APA91bHhPATf5etEvJiBgivWsCQW5KiFRA43e4pIGy_aHHSnfzcM5YFEWONWwBydbw1q_Br0OQgFxElhieEl1WXWzn5QLQKJ3TvR1_L56deIAaCS-V72AaHKdP4bKApvL6b0sxvSdnr7\"]}");
	curl_setopt($ch, CURLOPT_POST, 1);

	$headers = array();
	$headers[] = "Authorization: key=AIzaSyCBs6eWhNgm7IUbKC1u0f7NvHdDMHRR-rs";
	$headers[] = "Content-Type: application/json";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    return 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	return "Success: $result";
}
?>