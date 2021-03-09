<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);

    $wikiCountryName = preg_replace('/\s+/', '_', $_REQUEST['countryName']);
    
    $url='https://en.wikipedia.org/api/rest_v1/page/summary/' . $wikiCountryName .'?redirect=true';
    
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

	$wikiCountryExcerpt = json_decode($result,true);

    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['wikiCountryExcerpt'] = $wikiCountryExcerpt;

    echo json_encode($output);
?>