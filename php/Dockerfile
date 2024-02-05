FROM php:8.2-fpm

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev libgd-dev sendmail
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install pdo_mysql gd

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini
RUN echo "access.log = /dev/null" >> /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html