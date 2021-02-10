<?php

function getCountryBorder($cc) {           
    $countryBordersData = json_decode(file_get_contents(dirname(__DIR__)."/resources/countryBorders.geo.json"), false); 
    foreach ((array) $countryBordersData as $coll) {
        foreach ((array) $coll as $featuresColl){        
            foreach ((array)$featuresColl as $propertiesColl) {
                if (isset($propertiesColl -> iso_a3) && $propertiesColl -> iso_a3 == $cc) {
                    return $featuresColl;
                    break;
                }
            }
        }
    }
}

?>