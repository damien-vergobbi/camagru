FROM php:8.2-fpm

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev libgd-dev sendmail
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install pdo_mysql gd

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/conf.d/timezone.ini
RUN echo "access.log = /dev/null" >> /usr/local/etc/php-fpm.d/www.conf

# Echo ini settings
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory_limit.ini
RUN echo "upload_max_filesize = 10000M" > /usr/local/etc/php/conf.d/upload_max_filesize.ini
RUN echo "post_max_size = 10000M" > /usr/local/etc/php/conf.d/post_max_size.ini
RUN echo "max_execution_time = 600" > /usr/local/etc/php/conf.d/max_execution_time.ini
RUN echo "max_input_time = 600" > /usr/local/etc/php/conf.d/max_input_time.ini
RUN echo "max_input_vars = 10000" > /usr/local/etc/php/conf.d/max_input_vars.ini

# ini settings error (hide errors)
RUN echo "display_errors = Off" > /usr/local/etc/php/conf.d/display_errors.ini
RUN echo "log_errors = On" > /usr/local/etc/php/conf.d/log_errors.ini
RUN echo "error_log = /var/log/php_errors.log" > /usr/local/etc/php/conf.d/error_log.ini

# Right permissions to posts/ folder
# RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www/
RUN mkdir -p /var/www/html/public/posts
RUN chmod g+rwX /var/www/html/public/posts
# RUN chmod -R g+rwX /var/www

WORKDIR /var/www/html