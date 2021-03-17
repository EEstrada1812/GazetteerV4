<?php

    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);
    $countryFullName = $_REQUEST['countryFullName'];
    // $url='https://data.opendatasoft.com/api/records/1.0/search/?dataset=world-heritage-list%40public-us&q='.$countryFullName.'&rows=20&sort=date_inscribed&facet=category&facet=region&facet=states&refine.category=Cultural&refine.states='.$countryFullName;
    $url = 'https://userclub.opendatasoft.com/api/records/1.0/search/?dataset=world-heritage-list&q='.$countryFullName.'&lang=en&sort=date_inscribed&facet=category&facet=region&facet=states';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $unesco = json_decode($result,true);

    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['unescoSites'] = $unesco;

    echo json_encode($output);
?>