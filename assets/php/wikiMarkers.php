<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);
    
    $cityLat = $_REQUEST['lat'];
    $cityLng = $_REQUEST['lng'];
    $countryCodeA2 = $_REQUEST['countryCodeA2'];
            
    $url='http://api.geonames.org/findNearbyWikipediaJSON?formatted=true&lat=' . $cityLat . '&lng=' . $cityLng . '&country='. $countryCodeA2 .'&maxRows=20&username=estrada1107&style=full';
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $findNearby = json_decode($result,true);

    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['wikiCitiesData'] = $findNearby;;

    echo json_encode($output);
?>