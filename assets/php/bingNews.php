<?php
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    $executionStartTime = microtime(true);
    
    $accessKey = '548e929165324c1a8299320a99b97056';

    $endpoint = 'https://api.bing.microsoft.com/v7.0/news/search';

    $term = $_REQUEST['countryName'];

    function BingNewsSearch ($url, $key, $query) {
        $headers = "Ocp-Apim-Subscription-Key: $key\r\n";
        $options = array ('http' => array (
                            'header' => $headers,
                            'method' => 'GET' ));
        $context = stream_context_create($options);
        $result = file_get_contents($url . "?q=" . urlencode($query)."&originalImg=true&setLang=en-gb&mkt=en-GB&count=6", false, $context);
        $headers = array();
        foreach ($http_response_header as $k => $v) {
            $h = explode(":", $v, 2);
            if (isset($h[1]))
                if (preg_match("/^BingAPIs-/", $h[0]) || preg_match("/^X-MSEdge-/", $h[0]))
                    $headers[trim($h[0])] = trim($h[1]);
        }
        return array($headers, $result);
    }
    list($headers, $json) = BingNewsSearch($endpoint, $accessKey, $term);

    $bingNews = json_decode($json, true);

    //output status
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "success";
    $output['status']['executedIn'] = intval((microtime(true) - $executionStartTime) * 1000) . " ms";
    
    $output['data']['BingNews'] = $bingNews;

    echo json_encode($output);
?>