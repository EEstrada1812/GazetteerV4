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
    $wikiCountryName = null;

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
    $wikiCountryName = preg_replace('/\s+/', '_', $countryName);
    
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
        
    
    //PositionStack API Call for capital city long and lat
    $url ='http://api.positionstack.com/v1/forward?access_key=cc4a38f03554215037c505edf96abf81&query='. $capitalCityWiped .','.$countryNameNoSpace;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);
    
    curl_close($ch);
    
    $capitalData = json_decode($result,true);   
    
    $capitalLat = $capitalData['data'][0]['latitude'];
    $capitalLng = $capitalData['data'][0]['longitude'];
    
    //Weather Api
    $url='api.openweathermap.org/data/2.5/onecall?lat='. $capitalLat . '&lon='. $capitalLng .'&exclude=minutely,hourly,alerts&units=metric&appid=4ef2716ffdcebe56f05f86c5c6adb952';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $weather = json_decode($result,true);

    //Covid Api Call
    $url='https://corona-api.com/countries/'. $countryCodeA2;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $covid = json_decode($result,true);
    
    //Currency Exchange Rates
    $url='http://data.fixer.io/api/latest?access_key=fef7dc3a8df32be65a3006deb15fe2bf';
    //$url='http://data.fixer.io/api/latest?access_key=8bc8db0d02010c50047f53ccf9889388';
    //$url='https://openexchangerates.org/api/latest.json?app_id=172dd560a2bd4ea38005129d6fae498d';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $exchangeRates = json_decode($result,true);

    foreach ($exchangeRates['rates'] as $code => $rate) {
        if ($code ==  $currentCurrency) {
            $output['data']['currentRate'] = $rate;
            break;
        } else {
            $output['data']['currentRate'] = 'Rate not available';
        }
    }

    //Wiki Country Excerpt
    $url='https://en.wikipedia.org/api/rest_v1/page/summary/' . $wikiCountryName .'?redirect=true';
    
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

	$wikiCountryExcerpt = json_decode($result,true);	

    //Bing News API
    // $accessKey = '548e929165324c1a8299320a99b97056';

    // $endpoint = 'https://api.bing.microsoft.com/v7.0/news/search';

    // $term = $countryName;

    // function BingNewsSearch ($url, $key, $query) {
    //     $headers = "Ocp-Apim-Subscription-Key: $key\r\n";
    //     $options = array ('http' => array (
    //                         'header' => $headers,
    //                         'method' => 'GET' ));
    //     $context = stream_context_create($options);
    //     $result = file_get_contents($url . "?q=" . urlencode($query)."&originalImg=true&setLang=en-gb&mkt=en-GB&count=6", false, $context);
    //     $headers = array();
    //     foreach ($http_response_header as $k => $v) {
    //         $h = explode(":", $v, 2);
    //         if (isset($h[1]))
    //             if (preg_match("/^BingAPIs-/", $h[0]) || preg_match("/^X-MSEdge-/", $h[0]))
    //                 $headers[trim($h[0])] = trim($h[1]);
    //     }
    //     return array($headers, $result);
    // }
    // list($headers, $json) = BingNewsSearch($endpoint, $accessKey, $term);

    // $bingNews = json_decode($json, true);

    //UNESCO Sites
    // $url='https://data.opendatasoft.com/api/records/1.0/search/?dataset=world-heritage-list%40public-us&q='.$countryFullName.'&rows=20&sort=date_inscribed&facet=category&facet=region&facet=states&refine.category=Cultural&refine.states='.$countryFullName;
    $url = 'https://userclub.opendatasoft.com/api/records/1.0/search/?dataset=world-heritage-list&q='.$countryFullName.'&lang=en&sort=date_inscribed&facet=category&facet=region&facet=states';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);

    $result=curl_exec($ch);

    curl_close($ch);

    $unesco = json_decode($result,true);

    //capital city hospitals
    $url='https://discover.search.hereapi.com/v1/discover?at='.$capitalLat.','.$capitalLng.'&q=hospital&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=10&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityHospitals = json_decode($result,true);
    
    //capital city airports
    $url='https://discover.search.hereapi.com/v1/discover?at='.$capitalLat.','.$capitalLng.'&q=airport&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=15&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityAirports = json_decode($result,true);
    
    //capital city parks
    $url='https://discover.search.hereapi.com/v1/discover?at='.$capitalLat.','.$capitalLng.'&q=park&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=20&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityParks = json_decode($result,true);

    //capital city museums
    $url='https://discover.search.hereapi.com/v1/discover?at='.$capitalLat.','.$capitalLng.'&q=museum&lang=en-US&in=countryCode:'.$countryCodeA3.'&limit=25&apiKey=vUAsu-QX6rLWXv_WfJqiy4F94uhDCTj7aWfdLWMaiqM';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);

	$result=curl_exec($ch);

	curl_close($ch);

    $capCityMuseums = json_decode($result,true);

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
            $url='http://api.geonames.org/wikipediaSearchJSON?formatted=true&q=' . $cityName .'&maxRows=10&username=estrada1107&style=full';

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
    $output['data']['capitalData'] = $capitalData['data'][0];
    $output['data']['weather'] = $weather;
    $output['data']['covidData'] = $covid;
    $output['data']['exchangeRates'] = $exchangeRates;
    $output['data']['wikiCountryExcerpt'] = $wikiCountryExcerpt;
    //$output['data']['BingNews'] = $bingNews;
    $output['data']['unescoSites'] = $unesco;
    $output['data']['capCityHospitals'] = $capCityHospitals;
    $output['data']['capCityAirports'] = $capCityAirports;
    $output['data']['capCityParks'] = $capCityParks;
    $output['data']['capCityMuseums'] = $capCityMuseums;
    
    $output['data']['wikiCitiesTextData'] = $wikiCitiesTextData;

    echo json_encode($output);
?>