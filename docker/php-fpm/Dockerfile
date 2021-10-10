FROM php:8.0-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www

RUN apt-get update; \
    apt-get upgrade -y; \    
    # Install the PHP pdo_mysql extention
    docker-php-ext-install pdo_mysql;     

CMD composer install; php-fpm;

EXPOSE 9000 

