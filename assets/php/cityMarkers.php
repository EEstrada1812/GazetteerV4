<?php
    
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);

    $cityLat = $_REQUEST['lat'];
    $cityLng = $_REQUEST['lng'];
    $countryCodeA3 = $_REQUEST['countryCodeA3'];
    
    //capital city hospitals
    $url='https://discover.search.hereapi.com/v1/discover?at='.$cityLat.','.$cityLng.'&q=hospital&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=5&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityHospitals = json_decode($result,true);
    
    //capital city airports
    $url='https://discover.search.hereapi.com/v1/discover?at='.$cityLat.','.$cityLng.'&q=airport&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=5&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityAirports = json_decode($result,true);
    
    //capital city parks
    $url='https://discover.search.hereapi.com/v1/discover?at='.$cityLat.','.$cityLng.'&q=park&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=5&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityParks = json_decode($result,true);

    //capital city museums
    $url='https://discover.search.hereapi.com/v1/discover?at='.$cityLat.','.$cityLng.'&q=museum&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=5&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityMuseums = json_decode($result,true);




    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['capCityHospitals'] = $capCityHospitals;
    $output['data']['capCityAirports'] = $capCityAirports;
    $output['data']['capCityParks'] = $capCityParks;
    $output['data']['capCityMuseums'] = $capCityMuseums;

    echo json_encode($output);
?>