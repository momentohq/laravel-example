<head>
  <meta name="Momento Laravel cache driver example" content="Taggable Momento serverless cache driver example for Laravel">
</head>
<img src="https://docs.momentohq.com/img/logo.svg" alt="logo" width="400"/>

# Momento Laravel Example
In this repo, you will see an example of how to integrate a Momento cache into your Laravel app, instead of a Redis or Memcached.
What's the benefits, you ask? You don't have to worry about Redis/Memcached nodes!
Intrigued? Keep on reading!

## Run the app via Docker

Before building a Docker image, create `.env` file in `docker` directory with the following env variables:
- `MOMENTO_AUTH_TOKEN`=<YOUR_AUTH_TOKEN>
- `MOMENTO_CACHE_NAME`=<YOUR_CACHE_NAME>
- `WEATHER_API_KEY` this is for weather API. Check out [OpenWeather](https://openweathermap.org/) to get an API key.

If you don't have a Momento auth token, you can generate one using the 
[Momento CLI](https://github.com/momentohq/momento-cli).

Build a Docker image for the app:
```bash
cd docker
docker build --tag laravel-example .
```
**Note**: Building the `laravel-example` image involves compiling the PHP gRPC extension, which will take several minutes to complete.

If you run into the API limits from GitHub, add a GitHub personal access token to your composer configuration:
```bash
export COMPOSER_AUTH=<YOUR_GITHUB_ACCESS_TOKEN>
docker build --tag laravel-example --build-arg COMPOSER_AUTH=$COMPOSER_AUTH .
```

And run a Docker container with the image:
```bash
docker run -d --env-file .env -p 8000:8000 laravel-example
```

## Manual PHP Setup

You will need to install the following:

- [PHP](https://www.php.net/manual/en/install.php)
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [Laravel](https://laravel.com/docs/10.x/installation)
- [gRPC for PHP](https://cloud.google.com/php/grpc)

Add the repository and dependency to your project's `composer.json`:

```json
{
  "require": {
    "momentohq/laravel-example": "0.2.2"
  }
}
```

Add the Momento configuration to the 'stores' section of `config/cache.php`, adjusting the `cache_name` and 
`default_ttl` parameters as needed:

```php
'stores' => [
        ...
        'momento' => [
            'driver' => 'momento',
            'cache_name' => 'my-momento-cache',
            'default_ttl' => 60,
        ],
],
```

Update the cache driver entry in your `.env` file:

`CACHE_DRIVER=momento`

And add the following environment variables into your `.env` file:

- `WEATHER_API_KEY` this is for weather API. Check out [OpenWeather](https://openweathermap.org/) to get an API key.
- `MOMENTO_AUTH_TOKEN` 

If you don't have a Momento auth token, you can generate one using the 
[Momento CLI](https://github.com/momentohq/momento-cli).

Run `composer update` to install the necessary prerequisites.

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
Examples of different ways to use Momento's Laravel cache client and cache driver can be found in the 
[WeatherController.php](src/Controllers/WeatherController.php).

For examples of using the Momento client directly, check out our PHP SDK [examples](https://github.com/momentohq/client-sdk-php/tree/main/examples)!

A simple example usage of Momento's Laravel cache driver is extracted below:

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
