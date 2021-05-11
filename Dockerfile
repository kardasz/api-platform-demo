FROM php:7.4-fpm-alpine

ENV TZ Europe/London

ENV GLOBAL_COMPOSER_HOME /root/.composer
ENV PATH $PATH:/root/.composer/vendor/bin
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk --update add \
    su-exec \
    autoconf \
    g++ \
    make \
    sudo \
    openssh \
    git \
    tzdata \
    nginx \
    curl \
    ca-certificates \
    python3 \
    py-pip \
    icu \
    icu-dev \
    libxml2-dev \
    libxslt \
    libxslt-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libpng-dev \
    unzip && \
    pecl install redis && \
    docker-php-ext-enable redis && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-configure intl && \
    docker-php-ext-install bcmath pdo_mysql opcache intl soap zip && \
    pip install --upgrade supervisor && \
    rm /var/cache/apk/*

# TimeZone
RUN cp /usr/share/zoneinfo/Europe/London /etc/localtime && echo "Europe/London" >  /etc/timezone

RUN \
    rm -rf /var/www && \
    mkdir -p /var/www/public && \
    chown -R www-data:www-data /var/www/ && \
    cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    curl -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ADD ./docker-files/etc /etc

RUN printf "\nmemory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\ndisplay_errors = 1" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\ndisplay_startup_errors = 1" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\nmax_input_vars = 3000" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\ndate.timezone = Europe/London" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\nupload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\npost_max_size = 100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "\nerror_log = /proc/1/fd/2" >> /usr/local/etc/php/conf.d/custom.ini && \
    printf "[www]\npm = static\npm.max_children = 10\npm.status_path = /fpm-status" >> /usr/local/etc/php-fpm.d/zz-custom.conf

WORKDIR /var/www

ADD ./ /var/www

RUN composer -n --no-ansi install && \
    chown -R www-data:www-data var/

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]