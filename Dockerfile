FROM php:8.0-cli

RUN apt-get update
RUN apt-get install libzip-dev git -y
RUN docker-php-ext-install pdo pdo_mysql zip
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini