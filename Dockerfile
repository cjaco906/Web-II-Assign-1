FROM php:apache

RUN apt-get update && apt-get install -y

COPY ./ /var/www/html/

RUN chown -R www-data:www-data /var/www/html
