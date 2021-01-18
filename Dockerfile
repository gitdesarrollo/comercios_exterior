FROM php:latest

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client libmagickwand-dev --no-install-recommends

RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install zip

WORKDIR /app