<?php

namespace Momento\LaravelExample\Controllers;

use GuzzleHttp\Client;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Momento\Auth\EnvMomentoTokenProvider;
use Momento\Cache\CacheClient;
use Momento\Config\Configurations\Laptop;

class WeatherController extends Controller
{
    protected Client $httpClient;

    public function __construct()
    {
        $httpClient = new Client();
        $this->httpClient = $httpClient;
    }

    // This function uses Cache Facade with momento as a cache driver.
    // You need to update CACHE_DRIVER env variable to 'momento'
    public function city($city)
    {
        $apiKey = env("WEATHER_API_KEY");
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}";
        $result = Cache::get($city);
        if (!is_null($result)) {
            return $result;
        } else {
            $res = $this->httpClient->get($url);
            if ($res->getStatusCode() == 200) {
                $json = $res->getBody();
                // 10 minutes TTLc
                Cache::put($city, $json, 600);
                return $json;
            }
        }
    }

    // This function create a Momento client and uses it as cache.
    public function zipcode($zipcode, $countryCode)
    {
        $configuration = Laptop::latest();
        $authProvider = new EnvMomentoTokenProvider("MOMENTO_AUTH_TOKEN");
        $momentoClient = new CacheClient($configuration, $authProvider, 60);
        $cacheName = "zipcode-cache";
        $momentoClient->createCache($cacheName);
        $apiKey = env("WEATHER_API_KEY");
        $url = "https://api.openweathermap.org/data/2.5/weather?zip={$zipcode},{$countryCode}&appid={$apiKey}";
        $zipcodeWeatherInfo = "{$zipcode}-{$countryCode}";
        $result = $momentoClient->get($cacheName, $zipcodeWeatherInfo);
        if ($result->asHit()) {
            return $result->asHit()->valueString();
        }
        elseif ($result->asMiss()) {
            $res = $this->httpClient->get($url);
            if ($res->getStatusCode() == 200) {
                $json = $res->getBody();
                // 10 minutes TTLc
                $momentoClient->set($zipcodeWeatherInfo, $zipcodeWeatherInfo, $json, 600);
                return $json;
            }
        }
    }

    // This function uses MomentoTaggedCache to store and retrieve a key/value pair.
    public function cityId($cityId) {
        $apiKey = env("WEATHER_API_KEY");
        $url = "https://api.openweathermap.org/data/2.5/weather?id={$cityId}&appid={$apiKey}";
        $cityWeatherIdInfo = "weather-{$cityId}";
        $result = Cache::tags(['weather', $cityId])->get($cityWeatherIdInfo);
        if (!is_null($result)) {
            return $result;
        } else {
            $res = $this->httpClient->get($url);
            if ($res->getStatusCode() == 200) {
                $json = $res->getBody();
                // 10 minutes TTLc
                Cache::tags(['weather', $cityId])->put($cityWeatherIdInfo, $json, 600);
                return $json;
            }
        }
    }
}
