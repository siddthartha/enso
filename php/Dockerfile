FROM php:8.0-fpm

# COPY opcache.ini /usr/local/etc/php/conf.d/
COPY ./php.ini /usr/local/etc/php/
COPY ./www.conf /usr/local/etc/php/
COPY ./install-composer.sh /

VOLUME /root/.composer

RUN apt-get update && apt-get upgrade -y \
    && apt-get install apt-utils -y \
    && apt-get install git zip vim libzip-dev libgmp-dev libffi-dev libssl-dev wget -y \
    && docker-php-ext-install -j$(nproc) sockets zip gmp pcntl bcmath ffi pdo pdo_mysql \
#    && PHP_OPENSSL=yes pecl install ev \
#    && docker-php-ext-enable ev \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl install redis-stable \
    && docker-php-ext-enable redis \
    && docker-php-source delete \
    && apt-get autoremove --purge -y && apt-get autoclean -y && apt-get clean -y \
    && sh /install-composer.sh \
    && usermod -u 1000 www-data

EXPOSE 9000