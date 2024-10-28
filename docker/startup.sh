#!/bin/sh

# Define values for environment variables.

export PORT=${PORT:-'8080'}

export NGINX_WEB_ROOT=${NGINX_WEB_ROOT:-'/var/www/html/public'}
export NGINX_PHP_FALLBACK=${NGINX_PHP_FALLBACK:-'/index.php'}
export NGINX_PHP_LOCATION=${NGINX_PHP_LOCATION:-'\.php$'}
export NGINX_USER=${NGINX_USER:-'www-data'}
export NGINX_CONF=${NGINX_CONF:-'/etc/nginx/nginx.conf'}

export PHP_FPM_LISTEN=${PHP_FPM_LISTEN:-'127.0.0.1:9000'}
export PHP_USER=${PHP_USER:-'www-data'}
export PHP_GROUP=${PHP_GROUP:-'www-data'}
export PHP_MODE=${PHP_MODE:-'0660'}
export PHP_FPM_CONF=${PHP_FPM_CONF:-'/usr/local/etc/php-fpm.conf'}

export SUPERVISORD_CONF=${SUPERVISORD_CONF:-'/etc/supervisord.conf'}

export OPCACHE_CONF=${OPCACHE_CONF:-'/usr/local/etc/php/conf.d/opcache.ini'}

# Replace environment variables in configuration files.
envsubst '${NGINX_WEB_ROOT} ${PORT} ${NGINX_PHP_FALLBACK} ${NGINX_PHP_LOCATION} ${NGINX_USER} ${NGINX_CONF} ${PHP_FPM_LISTEN} ${PHP_USER} ${PHP_GROUP} ${PHP_MODE} ${PHP_FPM_CONF}' </tmp/nginx.tmp.conf >$NGINX_CONF
envsubst '${NGINX_WEB_ROOT} ${PORT} ${NGINX_PHP_FALLBACK} ${NGINX_PHP_LOCATION} ${NGINX_USER} ${NGINX_CONF} ${PHP_FPM_LISTEN} ${PHP_USER} ${PHP_GROUP} ${PHP_MODE} ${PHP_FPM_CONF}' </tmp/php-fpm.tmp.conf >$PHP_FPM_CONF
envsubst '${NGINX_WEB_ROOT} ${PORT} ${NGINX_PHP_FALLBACK} ${NGINX_PHP_LOCATION} ${NGINX_USER} ${NGINX_CONF} ${PHP_FPM_LISTEN} ${PHP_USER} ${PHP_GROUP} ${PHP_MODE} ${PHP_FPM_CONF}' </tmp/supervisord.tmp.conf >$SUPERVISORD_CONF
envsubst '${NGINX_WEB_ROOT} ${PORT} ${NGINX_PHP_FALLBACK} ${NGINX_PHP_LOCATION} ${NGINX_USER} ${NGINX_CONF} ${PHP_FPM_LISTEN} ${PHP_USER} ${PHP_GROUP} ${PHP_MODE} ${PHP_FPM_CONF}' </tmp/opcache.tmp.ini >$OPCACHE_CONF

# Set permissions for Laravel storage folder.
chown www-data:www-data -R /var/www/html/storage

# Run Laravel migration with fresh seeder if MIGRATE_AND_SEED is set to true.
if [ "$MIGRATE_AND_SEED" = true ] ; then
    # install dev dependencies
    /usr/bin/composer install --optimize-autoloader

    php artisan migrate:fresh --seed --force

    # Run Laravel composer install (without dev-dependencies this time)
    /usr/bin/composer install --no-dev --optimize-autoloader
fi

# Run Laravel migration (by force, since it would be a prod-environment)
php artisan migrate --force

# Run Supervisor to manage all processes.
/usr/bin/supervisord -n
