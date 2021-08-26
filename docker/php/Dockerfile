FROM php:7.4-fpm-alpine3.12

RUN apk upgrade --update \
    && apk add \
        freetype \
        zlib \
        icu \
        gettext \
        libzip \
        pcre \
        freetype-dev \
        zlib-dev \
        icu-dev \
        gettext-dev \
        libzip-dev \
        pcre-dev \
        g++ \
        make \
        autoconf \
        libtool \
        git \
    && NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NPROC} gettext intl exif mysqli pdo_mysql pcntl sockets zip opcache \
    && pecl install igbinary \
    && docker-php-ext-enable igbinary \
    && apk del --no-cache \
        freetype-dev \
        zlib-dev \
        icu-dev \
        gettext-dev \
        libzip-dev \
        g++ \
        make \
        autoconf \
        pcre-dev \
        libtool

RUN curl --silent --show-error https://getcomposer.org/installer > composer-setup.php \
    && php composer-setup.php --1 --install-dir=/bin --filename=composer \
    && rm composer-setup.php

COPY entry-point /usr/local/bin/entry-point
COPY ./usr/local/etc/php.ini /usr/local/etc/php/
COPY ./usr/local/etc/conf.d/docker-php-logs.ini /usr/local/etc/php/conf.d/

RUN chmod +x /usr/local/bin/entry-point

WORKDIR /var/www/symfony

# Expose port 9000 and start php-fpm server
EXPOSE 9000

ENTRYPOINT ["entry-point"]
CMD ["php-fpm"]
