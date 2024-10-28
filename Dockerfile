FROM php:8.2-fpm-alpine
RUN docker-php-ext-install pdo_mysql opcache

RUN apk add --no-cache nginx wget npm supervisor envsubst

RUN mkdir -p /run/nginx
RUN mkdir -p /run/php && touch /run/php/php-fpm.sock && touch /run/php/php-fpm.pid
RUN mkdir -p /run/php-fpm

COPY docker/nginx.tmp.conf /tmp/nginx.tmp.conf
COPY docker/php-fpm.tmp.conf /tmp/php-fpm.tmp.conf
COPY docker/supervisord.tmp.conf /tmp/supervisord.tmp.conf
COPY docker/opcache.tmp.ini /tmp/opcache.tmp.ini


RUN mkdir -p /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN cd /var/www/html && \
    /usr/bin/composer install --optimize-autoloader --no-dev && \
    npm install && \
    npm run build && \
    php artisan view:cache

COPY docker/cron-job /etc/cron.d/cron-job

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron-job

# Apply cron job
RUN crontab /etc/cron.d/cron-job

ENTRYPOINT [ "/var/www/html/docker/startup.sh" ]
