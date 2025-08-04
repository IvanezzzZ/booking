FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libxml2-dev \
    git && \
    docker-php-ext-install pdo pdo_pgsql xml zip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

RUN groupadd -g 1000 devgroup && \
    useradd -u 1000 -g devgroup -m devuser

USER devuser

WORKDIR /var/www/
