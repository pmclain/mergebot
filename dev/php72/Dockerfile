FROM php:7.2-fpm

LABEL MAINTAINER="Patrick McLain <pat@pmclain.com>"

# Install dependencies
RUN apt-get update \
  && apt-get install -y \
    zlib1g-dev \
    libxml2-dev \
    libsodium-dev \
    openssl \
    sudo \
    rsyslog \
    git \
    libssl-dev \
    mysql-client

# Install required PHP extensions

RUN docker-php-ext-install \
  bcmath \
  zip \
  pdo_mysql \
  sockets

RUN pecl install -o -f xdebug
RUN pecl install -o -f mongodb
RUN docker-php-ext-enable mongodb

VOLUME /root/.composer/cache
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PHP_MEMORY_LIMIT 756M
ENV XDEBUG_REMOTE_HOST docker.for.mac.localhost
ENV XDEBUG_ENABLE true

ADD etc/php-fpm.ini /usr/local/etc/php/conf.d/zz-php-fpm.ini
ADD etc/php-xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini

ADD docker-entrypoint.sh /docker-entrypoint.sh

RUN ["chmod", "+x", "/docker-entrypoint.sh"]

ENTRYPOINT ["/docker-entrypoint.sh"]

ADD etc/php-fpm.conf /usr/local/etc/

CMD ["php-fpm", "-F", "-R"]