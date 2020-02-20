#!/usr/bin/env sh

COMPOSER_FLAGS="--no-progress --prefer-dist --profile --no-suggest"
if [[ ${APP_ENV:="dev"} == "prod" ]]; then
    COMPOSER_FLAGS="--no-dev --optimize-autoloader --no-progress --prefer-dist --no-suggest"
fi

touch /tmp/DOING_COMPOSER_INSTALL

composer install ${COMPOSER_FLAGS}

chown -R www-data:www-data /app
chown -R www-data:www-data ${COMPOSER_HOME:="/var/lib/composer"}

rm /tmp/DOING_COMPOSER_INSTALL

/usr/local/bin/docker-php-entrypoint "$@"
