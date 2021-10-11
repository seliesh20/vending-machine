FROM php:fpm-alpine
RUN apk --update --no-cache add git \    
    && docker-php-ext-install mysqli pdo_mysql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD php-fpm; composer install;  
EXPOSE 9000