<?php
ini_set('memory_limit', '1024M');
function sortCitiesOfCountry($countryCode) {           
    $cities = json_decode(file_get_contents(dirname(__DIR__)."/resources/cities.json"), false); 
    
    $filteredCities = [];

    foreach ((array) $cities as $city) {
        if ($city->country == $countryCode && $city->population > 50000) {
            array_push($filteredCities, $city);
        }
     }
     return $filteredCities;
}

?>