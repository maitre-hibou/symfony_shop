FROM        php:7.3-fpm-alpine
MAINTAINER  Arnaud Ponel <arnaud@xpressive.io>

RUN         apk add --no-cache \
            --repository http://dl-cdn.alpinelinux.org/alpine/edge/main \
            --repository  http://dl-cdn.alpinelinux.org/alpine/edge/community \
            freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev libzip-dev shadow zlib-dev

RUN         usermod -u 1000 www-data && \
            groupmod -g 1000 www-data

RUN         mkdir -p /app
COPY        --chown=www-data:www-data . /app

RUN         mkdir -p /build
COPY        ./docker/services/app /build

RUN         docker-php-ext-configure gd \
            --with-freetype-dir=/usr/include/ \
            --with-jpeg-dir=/usr/include/ \
            --with-webp-dir=/usr/include/
RUN         docker-php-ext-install gd pdo_mysql zip

ENV         COMPOSER_HOME=/var/lib/composer
RUN         mkdir -p $COMPOSER_HOME
RUN         chown -R www-data:www-data $COMPOSER_HOME
RUN         cd /tmp && /build/install_composer.sh

USER        www-data
RUN         composer global require hirak/prestissimo
USER        root

VOLUME      /app
WORKDIR     /app

ENV         APP_ENV=dev

ENTRYPOINT  ["/build/entrypoint.sh"]
CMD         ["php-fpm"]
