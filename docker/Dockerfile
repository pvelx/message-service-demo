FROM php:7.4-fpm-alpine

WORKDIR '/app'

# Install essential build tools
RUN apk add --no-cache \
    git \
    yarn \
    autoconf \
    g++ \
    make \
    openssl-dev \
    zip \
    unzip \
    zlib-dev \
    linux-headers \
    rabbitmq-c-dev \
    supervisor


# Install composer
ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer


# Setup bzip2 extension
RUN apk add --no-cache \
    bzip2-dev \
    && docker-php-ext-install -j$(nproc) bz2 \
    && docker-php-ext-enable bz2 \
    && rm -rf /tmp/*


# Install intl extension
RUN apk add --no-cache \
    icu-dev \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-enable intl \
    && rm -rf /tmp/*


# Install mysql extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install amqp extension
RUN pecl install amqp && docker-php-ext-enable amqp

# Install grpc extension for PHP
RUN pecl install grpc-1.33.1
RUN docker-php-ext-enable grpc

RUN rm /etc/supervisord.conf
COPY supervisor/ /etc/supervisor/

COPY . .
COPY ./docker/php.ini /usr/local/etc/php/php.ini

RUN composer install

RUN chown -R www-data:www-data /app

EXPOSE 9000
