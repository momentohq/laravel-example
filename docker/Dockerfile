FROM php:8.1.12-cli
ARG COMPOSER_AUTH
ENV COMPOSER_AUTH=$COMPOSER_AUTH

RUN apt-get update && apt-get install -y -q git rake ruby-ronn zlib1g-dev && apt-get clean

RUN cd /usr/local/bin && curl -sS https://getcomposer.org/installer | php
RUN cd /usr/local/bin && mv composer.phar composer
RUN pecl install grpc
RUN docker-php-ext-enable grpc

RUN composer create-project laravel/laravel laravel-example
WORKDIR /laravel-example
COPY .env ./
COPY cache.php ./config/cache.php
COPY composer.json composer.lock ./
RUN composer install
RUN php artisan config:clear
CMD php artisan serve --host=0.0.0.0 --port=8000