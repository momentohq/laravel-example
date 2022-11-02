FROM php:8.1.12-cli

RUN apt-get update && apt-get install -y -q git rake ruby-ronn zlib1g-dev && apt-get clean

RUN cd /usr/local/bin && curl -sS https://getcomposer.org/installer | php
RUN cd /usr/local/bin && mv composer.phar composer
RUN pecl install grpc
RUN docker-php-ext-enable grpc

COPY . /app

WORKDIR /app

RUN composer install

ARG token
ARG key
ENV MOMENTO_AUTH_TOKEN=$token
ENV MOMENTO_CACHE_NAME="laravel-cache"
ENV API_KEY=$key
ENV CACHE_DRIVER=momento

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port=8000"]
