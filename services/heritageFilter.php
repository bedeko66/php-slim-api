<?php

function sortWorldHeritagesByCountry($countryCode) {           
    $heritageData = json_decode(file_get_contents(dirname(__DIR__)."/resources/worldHeritageList.json"), false); 
    $filtered = [];

    foreach ((array) $heritageData as $heritage) {
        foreach($heritage->countries->iso as $isoCode) {
            if($isoCode == strtolower($countryCode)) {
                array_push($filtered, $heritage);
            }
        }
     }
     return $filtered;
}

?>