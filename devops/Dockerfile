FROM nginx:1.25.5-alpine

ENV PHP_VERSION=82 \
    COMPOSER_MEMORY_LIMIT=-1

# Intall dependencies
RUN apk update && \
    apk --update --no-cache add curl \
    supervisor \
    mysql-client \
    git \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-pdo \
    php${PHP_VERSION}-pdo_mysql \
    php${PHP_VERSION}-pdo_sqlite \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-session \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-apcu \
    php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-xmlwriter \
    php${PHP_VERSION}-pecl-apcu \
    php${PHP_VERSION}-xmlreader \
    php${PHP_VERSION}-phar \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-json \
    php${PHP_VERSION}-simplexml \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-sodium \
    php${PHP_VERSION}-exif \
    php${PHP_VERSION}-iconv \
    php${PHP_VERSION}-zlib && \
    rm -rf /var/cache/apk/* && \
    rm -rf /tmp/* 77 && \
    curl -sS https://getcomposer.org/installer | php -- --version=2.7.2 --install-dir=/usr/local/bin --filename=composer

# Copy configs files
COPY devops/assets/php-fpm.conf /etc/php-fpm.conf
COPY devops/assets/supervisord.conf /etc/supervisord.conf
COPY devops/assets/nginx/nginx.conf /etc/nginx/nginx.conf
COPY devops/assets/nginx/php-fpm.conf /etc/nginx/sites-enabled/php-fpm.conf
COPY devops/assets/php.ini /etc/php82/conf.d/custom.ini
COPY devops/entrypoint.sh /usr/sbin/entrypoint.sh

RUN chmod +x /usr/sbin/entrypoint.sh && \
    mkdir -p /run/php-fpm/

# App
WORKDIR /var/www/html
ARG CACHE_DATE=2024-01-01
COPY . ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

EXPOSE 80
ENTRYPOINT /usr/sbin/entrypoint.sh
