#!/bin/bash

# Substitute in php.ini values
[ ! -z "${XDEBUG_REMOTE_HOST}" ] && sed -i "s/!XDEBUG_REMOTE_HOST!/${XDEBUG_REMOTE_HOST}/" /usr/local/etc/php/conf.d/zz-xdebug.ini

[ "$XDEBUG_ENABLE" = "true" ] && \
    docker-php-ext-enable xdebug && \
    echo "Xdebug is enabled"

[ ! -z "${COMPOSER_GITHUB_TOKEN}" ] && \
    composer config -g github-oauth.github.com $COMPOSER_GITHUB_TOKEN

exec "$@"
