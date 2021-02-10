<?php

function fetch_api($api) {
   
    $executionStartTime = microtime(true) / 1000;
    $url = $api[0];
    $dataKey = $api[1];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);

    $decode = json_decode($result,true);	
    
    header('Content-Type: application/json; charset=UTF-8');
    $output['status']['code'] = "200";
    $output['status']['name'] = "ok";
    $output['status']['description'] = "mission saved";
    $output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
    
    if($dataKey == ''){
        $output['data'] = $decode;
    } else {
        $output['data'] = $decode[$dataKey];
    }
    return $output['data']; 
};

?>