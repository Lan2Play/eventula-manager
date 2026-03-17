#!/bin/sh

echo "$DB_HOST:$DB_PORT IS LIVE!"

### ---- Do NOT edit below this line ---- ###

# Generate the App Key if it doesn't Exist
APP_KEY_FLAG=false
if [ ! -n "$APP_KEY" ]
then
	export APP_KEY=$(php artisan key:generate --show | cut -d "[" -f2 | cut -d "]" -f1)
	echo "---------------"
	echo "Cannot Find APP_KEY. Generating......"
	echo "YOUR APP KEY IS - ${APP_KEY} - KEEP THIS SAFE!"
	echo "---------------"
	sleep 15
	APP_KEY_FLAG=true
fi

# Add the symlinks for logs to allow NGINX & set Laravel to log to file instead of to stdout
if [ -n "$LOG_FILES" ]
then
	if [ "$LOG_FILES" = "true" ]
	then
		echo "---------------"
		echo "LOG_FILES set to true. Writing logs to disk......"
		rm /var/log/nginx/access.log
		rm /var/log/nginx/error.log
 		ln -sf $NGINX_DOCUMENT_ROOT/storage/logs/access.log /var/log/nginx/access.log
		ln -sf $NGINX_DOCUMENT_ROOT/storage/logs/error.log /var/log/nginx/error.log
		echo "YOUR LOGS CAN BE FOUND IN $NGINX_DOCUMENT_ROOT/storage/logs/ WITHIN THE CONTAINER"
		echo "---------------"
		export APP_LOG="single"
	fi
fi

# Clear the config cache and load in new Variables
php artisan config:clear
php artisan config:cache

# Cache routes for production performance
php artisan route:clear
php artisan route:cache

# Only migrate database if DB_MIGRATE env variable is set to true
if [ -n "$DB_MIGRATE" ]
then
	if [ "$DB_MIGRATE" = "true" ]
	then
		php artisan migrate --force
	fi
fi
if [ -n "$DB_SEED" ]
then
	if [ "$DB_SEED" = "true" ]
	then
		php artisan db:seed --class=RequiredDatabaseSeeder --force
	fi
fi

if [ "$APP_KEY_FLAG" = "true" ]
then
	echo "---------------"
	echo "ATTENTION! EXTRA ACTION NEEDED:"
	echo "You must now restart the manager with the 'APP_KEY=${APP_KEY}' env variable set."
	echo "Without this, the manager will not work!"
	echo "Keep this safe!"
	echo "---------------"
fi

# Supervisor Default
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
