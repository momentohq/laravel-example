<head>
  <meta name="Momento Laravel cache driver example" content="Taggable Momento serverless cache driver example for Laravel">
</head>
<img src="https://docs.momentohq.com/img/logo.svg" alt="logo" width="400"/>

# Momento Laravel Example
In this repo, you will see an example of how to integrate a Momento cache into your Laravel app, instead of a Redis or Memcached.
What's the benefits, you ask? You don't have to worry about Redis/Memcached nodes!
Intrigued? Keep on reading!

## Run the app via Docker

Build a Docker image for the app:

Coming soon.

## Manual PHP Setup

Need to install the following:

- [PHP](https://www.php.net/manual/en/install.macosx.packages.php)
- [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- [gRPC for PHP](https://cloud.google.com/php/grpc)

Add the repository and dependency to your project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/momentohq/laravel-example"
    },
    {
      "type": "vcs",
      "url": "https://github.com/momentohq/laravel-cache"
    },
    {
      "type": "vcs",
      "url": "https://github.com/momentohq/client-sdk-php"
    }
  ],
  "require": {
    "momentohq/laravel-example": "0.1.0",
    "momentohq/laravel-cache": "0.1.3",
    "momentohq/client-sdk-php": "0.2.1"
  }
}
```

Finally, add the required config to your `config/cache.php`:

```php
'default' => env('CACHE_DRIVER', 'momento'),

'stores' => [
        'momento' => [
            'driver' => 'momento',
            'cache_name' => env('MOMENTO_CACHE_NAME'),
            'default_ttl' => 60,
        ],
],
```

Run `composer update` to install the necessary prerequisites.

You need to set the following environment variables:

- `WEATHER_API_KEY` this is for weather API. Check out [OpenWeather](https://openweathermap.org/) to get an API key.
- `MOMENTO_AUTH_TOKEN`
- `MOMENTO_CACHE_NAME`

To run this application:

```bash
php artisan serve
```

## cURL commands
```bash
curl http://127.0.0.1:8000/api/weather/<your-favorite-city>

Example:
curl http://127.0.0.1:8000/api/weather/denver
```

or

```bash
curl http://127.0.0.1:8000/api/weather/<your-favorite-zipcode>/<country-code-such-as-us>

Example:
curl http://127.0.0.1:8000/api/weather/98101/us
```

or

```bash
curl http://127.0.0.1:8000/api/weather/id/<your-favorite-city-id>

City id can be found here: http://bulk.openweathermap.org/sample/

Example:
curl http://127.0.0.1:8000/api/weather/id/833
```

## Exploring the Momento Cache Integration
How to use Momento's Laravel cache client and cache driver can be found in (WeatherController.php)[src/Controllers/WeatherController.php].

For examples of using the Momento client directly, check out our PHP SDK [examples](https://github.com/momentohq/client-sdk-php/tree/main/examples)!

Use Momento as a Laravel cache driver:
```php
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
```
