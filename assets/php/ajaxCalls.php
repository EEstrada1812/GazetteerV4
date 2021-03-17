<?php
    $border = null;
    $countryCodeA2 = null;
    $countryCodeA3 = null;
    $capitalCity = null;
    $countryFullName = null;

    $countryName = null;
    $countryNameNoSpace = null;
    $capitalLat = null;
    $capitalLng = null;
    $capitalCityNoSpace = null;
    $capitalCityWiped = null;
    $currentCurrency = null;
   

    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);

    $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
    'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c','è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i','ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o','ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s', 'ü'=>'u', 'ă'=>'a', 'Ă'=>'A', 'ș'=>'s', 'Ș'=>'S', 'ț'=>'t', 'Ț'=>'T' );

    // get country border feature
    $countryBorders = json_decode(file_get_contents("../data/countryBorders.geo.json"), true);

        foreach ($countryBorders['features'] as $feature) {
            if ($feature["properties"]["iso_a2"] ==  $_REQUEST['countryCode']) {
                $border = $feature;
                break;
            }
        }

    $countryName = $border['properties']['name'];
    $countryNameNoSpace = preg_replace('/\s+/', '%20', $countryName);
    
    $countryCodeA2 = $border['properties']['iso_a2'];
    $countryCodeA3 = $border['properties']['iso_a3'];
    
    // RestCountries API Call for capital city and currrency info
    $url='https://restcountries.eu/rest/v2/alpha/'. $countryCodeA2;
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $restCountries = json_decode($result,true); 
    
    $countryFullName = $restCountries['name'];
    $capitalCity = $restCountries['capital']; 
    $capitalCityNoSpace =  preg_replace('/\s+/', '%20', $capitalCity);
    $capitalCityWiped = strtr( $capitalCityNoSpace, $unwanted_array );
    $currentCurrency = $restCountries['currencies'][0]['code'];
        
    //large cities in country
    $url='https://public.opendatasoft.com/api/records/1.0/search/?dataset=geonames-all-cities-with-a-population-1000&q=&rows=10&sort=population&facet=timezone&facet=country&refine.country_code='. $countryCodeA2;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $largeCities = json_decode($result,true);
    $output['data']['largeCities'] = $largeCities['records'];


    $wikiCitiesTextData = array();
    foreach ($largeCities['records'] as $key => $value) {
        $cityName = preg_replace('/\s+/', '%20', $value['fields']['name']);
            //wiki city wiki text info
            $url='http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $cityName .'&maxRows=3&username=estrada1107&style=full';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$url);

            $result=curl_exec($ch);

            curl_close($ch);
            $cityTxtData = json_decode($result,true);
            
            array_push($wikiCitiesTextData, $cityTxtData);
    }


    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['border'] = $border;
    $output['data']['restCountries'] = $restCountries;
    $output['data']['wikiCitiesTextData'] = $wikiCitiesTextData;

    echo json_encode($output);
?>