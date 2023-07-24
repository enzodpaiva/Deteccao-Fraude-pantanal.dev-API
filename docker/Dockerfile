ARG PHP_VERSION=8.1.9
FROM php:${PHP_VERSION}-fpm-alpine
LABEL maintainer="Vertex Digital"

RUN apk update && apk add --no-cache --update \
    $PHPIZE_DEPS \
    libzip-dev \
    g++ \
    gcc \
    curl \
    curl-dev \
    zip \
    unzip \
    moreutils \
    icu-dev \
    freetype \
    freetype-dev \
    libmcrypt-dev \
    imagemagick \
    imagemagick-dev

RUN docker-php-ext-install \
    gd \
    exif \
    opcache \
    pcntl \
    zip \
    intl \
    && php -m \
    && rm -rf /var/lib/apt/lists/* ;

# Installing Mcrypt extension
RUN pecl install -o -f mcrypt && docker-php-ext-enable mcrypt

RUN pecl install imagick && docker-php-ext-enable imagick

RUN pecl config-set php_ini "${PHP_INI_DIR}/conf.d"

# Installing Mongodb extension
RUN pecl install mongodb \
    &&  echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongo.ini

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod 777 -R /var/www/html/
#copy the lumen.ini witch is the project custom .ini file with some php configs
COPY ./lumen.ini /usr/local/etc/php/conf.d/lumen.ini 