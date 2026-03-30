FROM php:8.3-cli-alpine

RUN apk add --no-cache --update linux-headers $PHPIZE_DEPS \
    postgresql-dev \
    zlib-dev  \
    libxpm-dev \
    libzip-dev \
    zip \
    unzip \
    bash  \
    coreutils  \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    acl \
    fcgi \
    file \
    gettext \
    icu-dev \
    git \
    && docker-php-ext-configure gd \
       --with-freetype=/usr/include/ \
       --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && docker-php-ext-install pdo_mysql intl \
    && docker-php-ext-install \
     intl \
     pdo_mysql \
     zip \
     opcache \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql \
    && apk del git \
    && rm -rf /tmp/*

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php/conf.d /usr/local/etc/php/conf.d

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

WORKDIR /app

USER app
