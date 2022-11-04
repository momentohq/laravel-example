# Momento Laravel Example

## Run the app via Docker

Build a Docker image for the app:

Coming soon.

---

## If you're interested in setting up PHP...

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
    }
  ],
  "require": {
    "momentohq/laravel-example": "dev-main"
  }
}
```

Run `composer update` to install the necessary prerequisites.
If your app is not able to automatically discover packages, run `php artisan package:discover` alternatively.

You need to the following env variables:

- `WEATHER_API_KEY` this is for weather API. Check out [OpenWeather](https://openweathermap.org/) to get an API key.
- `MOMENTO_AUTH_TOKEN`
- `MOMENTO_CACHE_NAME`

To run this application:

```bash
php artisan serve
```

Once it's running, in another Terminal window type:

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
