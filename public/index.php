<?php
require '../vendor/autoload.php';
require '../config/config.php';

require '../services/curl.php';
require '../services/borderFilter.php';
require '../services/cityFilter.php';
require '../services/heritageFilter.php';

$cont = new \Slim\Container($configuration);
$app = new \Slim\App($cont);


$app->options('/api/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


$app->get('/api', function($request, $response, $args) {
    return $response->write('Serving...');
});

$app->get('/api/countries', function($request, $response, $args) {
    $countries = json_decode(file_get_contents(dirname(__DIR__)."/resources/countries.json"), false); 
    return $response->withJson($countries);
});

function startsWith($string, $substring) {
    $len = strlen($substring);
    $hm = (substr($string, 0, $len) == $substring);
    return (substr($string, 0, $len) == $substring);
}

$app->get('/api/countries/search', function($request, $response, $args) {
    $countriesRaw = file_get_contents(dirname(__DIR__)."/resources/countries.json");
    $countries = json_decode($countriesRaw, TRUE);

    
    $term = $request->getQueryParams()['term'];
    
    $filteredCountries = array();
    foreach($countries as $key => $value) {
        if (startsWith(strtolower($value['name']), strtolower($term))) {
            array_push($filteredCountries, $value);
        }
    }
    return $response->withJson($filteredCountries);
});

$app->post('/api/restcountries', function($request, $response, $args) {

    $body = json_decode($request->getBody());
    $url = 'https://restcountries.eu/rest/v2/alpha/'. $body->countryCode;
    $data = fetch_api(array($url, ''));
    
    return $response->withJson($data);
});


$app->get('/api/geocountries', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url = 'https://secure.geonames.org/countryInfoJSON?&username=' . getenv('API_GEONAMES');
    $data = fetch_api(array($url, 'geonames'));
    
    return $response->withJson($data);
});

$app->post('/api/geocodingforward', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $body->spot . '&key=' . getenv('API_GEOCODING'); 
    $data = fetch_api(array($url,'results'));
    return $response->withJson($data);
});

$app->post('/api/geocodingreverse', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url = 'https://api.opencagedata.com/geocode/v1/json?q=' . $body->lat . '+' . $body->lon .'&key=' . getenv('API_GEOCODING'); 
    $data = fetch_api(array($url, 'results'));
    return $response->withJson($data);
});

$app->post('/api/weathercurrent', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url = 'api.openweathermap.org/data/2.5/weather?lat=' . $body->lat . '&lon=' . $body->lon. '&appid=' . getenv('API_WEATHER') . '&units=metric';
    $data = fetch_api(array($url, ''));
    return $response->withJson($data);
});

$app->post('/api/wikiinfo', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url =  'https://secure.geonames.org/wikipediaSearchJSON?q=' . $body->spot . '&maxRows=10&username=' . getenv('API_GEONAMES'); 
    $data = fetch_api(array($url, 'geonames'));
    return $response->withJson($data);
});

$app->get('/api/currencieslatest', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url = 'https://openexchangerates.org/api/latest.json?app_id=' .getenv('API_CURRENCIES');
    $data = fetch_api(array($url, ''));
    return $response->withJson($data);
});

$app->post('/api/countrydata', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $url =  'https://secure.geonames.org/countryInfoJSON?country='  . $body->countryCode .'&username=' . getenv('API_GEONAMES');
    $data = fetch_api(array($url, 'geonames'));
    return $response->withJson($data);
});


$app->post('/api/countryborder', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $borderData = getCountryBorder($body->countryCode);
    return $response->withJson($borderData);
});

$app->post('/api/cities', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $cities = sortCitiesOfCountry($body->countryCode);
    return $response->withJson($cities);
});

$app->post('/api/world_heritages', function($request, $response, $args) {
    
    $body = json_decode($request->getBody());
    $sortedHeritages = sortWorldHeritagesByCountry($body->countryCode);
    return $response->withJson($sortedHeritages);
});



$app->map(['GET', 'POST', 'OPTIONS'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; 
    return $handler($req, $res);
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});


$app->run();

?>