FROM harbor.ulbricht.casa/jinya/jinya-cms-php-base-test-image:latest

RUN install-php-extensions pdo pdo_mysql zip xdebug
#RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini
RUN echo "xdebug.mode=profile" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && echo "xdebug.output_dir=/var/www/html/profile" >> /usr/local/etc/php/conf.d/xdebug.ini
