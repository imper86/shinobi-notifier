FROM php:8.1-cli
ENV APP_ENV=prod
RUN apt-get update && \
    apt-get install supervisor libzip-dev zip -y --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN docker-php-ext-install zip sockets
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN rm composer-setup.php
RUN mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
COPY . /usr/src/app
COPY ./docker/supervisor /etc/supervisor/conf.d
WORKDIR /usr/src/app
RUN /usr/bin/composer install --no-dev
RUN ./vendor/bin/rr get-binary -q -n
CMD ["./bin/run"]