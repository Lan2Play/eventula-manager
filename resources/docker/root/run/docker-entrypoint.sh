#!/bin/bash
set -eo pipefail
shopt -s nullglob

# Translate Variables and Files to Environment
file_env() {
	local var="$1"
	echo -n "Setting $var ... "
	local fileVar="${var}_FILE"
	local def="${2:-}"
	if [ "${!var:-}" ] && [ "${!fileVar:-}" ]; then
		echo >&2 "error: both $var and $fileVar are set (but are exclusive)"
		exit 1
	fi
	local val="$def"
	if [ "${!var:-}" ]; then
		val="${!var}"
	elif [ "${!fileVar:-}" ]; then
		val="$(< "${!fileVar}")"
	fi
	export "$var"="$val"
	unset "$fileVar"
}



# Check Variables Exist & Translate from file
# Required Env Variables
echo 'Required Env Variable Check:'
file_env 'APP_URL'
if [ -z "$APP_URL" ];
then
	echo >&2 'ERROR'
	echo >&2 'Event Manager App is uninitialized because APP_URL is not specified '
	echo >&2 'You need to specify APP_URL'
	exit 1
else
	echo 'OK'
fi
file_env 'APP_EMAIL'
if [ -z "$APP_EMAIL" ];
then
	echo >&2 'ERROR'
	echo >&2 'Event Manager App is uninitialized because APP_EMAIL is not specified '
	echo >&2 'You need to specify APP_EMAIL'
	exit 1
else
	echo 'OK'
fi
file_env 'DB_PORT'
if [ -z "$DB_PORT" ];
then
	echo >&2 'ERROR'
	echo >&2 'Event Manager App is uninitialized because DB_PORT is not specified '
	echo >&2 'You need to specify DB_PORT'
	exit 1
else
	echo 'OK'
fi

file_env 'DB_HOST'
if [ -z "$DB_HOST" ];
then
	echo >&2 'ERROR'
	echo >&2 'Event Manager App is uninitialized because DB_HOST is not specified '
	echo >&2 'You need to specify DB_HOST'
	exit 1
else
	echo 'OK'
fi

file_env 'DB_PASSWORD'
if [ -z "$DB_PASSWORD" ];
then
	echo >&2 'ERROR'
	echo >&2 'Event Manager App is uninitialized because DB_PASSWORD is not specified '
	echo >&2 'You need to specify DB_PASSWORD'
	exit 1
else
	echo 'OK'
fi


# Optional Env Variables
echo 'Option Env Variable Check:'
file_env 'MAIL_HOST'
if [ -z "$MAIL_HOST" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi
file_env 'MAIL_USERNAME'
if [ -z "$MAIL_USERNAME" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi
file_env 'MAIL_PASSWORD'
if [ -z "$MAIL_PASSWORD" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi
file_env 'STEAM_API_KEY'
if [ -z "$STEAM_API_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'CHALLONGE_API_KEY'
if [ -z "$CHALLONGE_API_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'APP_KEY'
if [ -z "$APP_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'CHALLONGE_API_KEY'
if [ -z "$CHALLONGE_API_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'STRIPE_PUBLIC_KEY'
if [ -z "$STRIPE_PUBLIC_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'STRIPE_SECRET_KEY'
if [ -z "$STRIPE_SECRET_KEY" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'PAYPAL_USERNAME'
if [ -z "$PAYPAL_USERNAME" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'PAYPAL_PASSWORD'
if [ -z "$PAYPAL_PASSWORD" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi

file_env 'PAYPAL_SIGNATURE'
if [ -z "$PAYPAL_SIGNATURE" ];
then
	echo 'NOT SET'
else
	echo 'OK'
fi


file_env 'APPEAR_DISABLE_CUSTOM_CSS_LINKING'
if [ -z "$APPEAR_DISABLE_CUSTOM_CSS_LINKING" ];
then
	echo 'NOT SET, DEFAULTING TO FALSE'
	export APPEAR_DISABLE_CUSTOM_CSS_LINKING=false
fi
	
if [ "$APPEAR_DISABLE_CUSTOM_CSS_LINKING" = 'true' ];
then
	echo 'CUSTOM_CSS_LINKING IS DISABLED, THIS IS THE RIGHT SETTING WHEN YOUR SRC FOLDER IS MOUNTED !! DONT USE THIS IN PRODUCTION !!! '
else
	echo 'CUSTOM_CSS_LINKING IS ENABLED,THIS IS THE RIGHT SETTING IN PRODUCTION '
fi


file_env 'APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS'
if [ -z "$APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS" ];
then
	echo 'NOT SET, DEFAULTING TO FALSE'
	export APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS=false
fi
	
if [ "$APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS" = 'true' ];
then
	echo 'THE CSS APPEARANCE SETTINGS IN THE ADMIN MENU ARE DISABLED. THIS IS ONLY INTENDED IF YOU MOUNT YOUR CUSTOM SCSS (SEE DOCUMENTATION)'
fi



file_env 'ENV_OVERRIDE'
if [ -n "$ENV_OVERRIDE" ] && [ "$ENV_OVERRIDE" = 'true' ];
then
	echo 'WARNING!'
	echo 'ENV OVERRIDE IS SET!'
	echo 'ALL API KEYS WILL BE OVERWRITTEN BY THE ENV VARIABLES'
	echo 'DATABASE API KEYS WILL NOT BE USED'
else
	echo 'ENV OVERRIDE IS NOT SET!'
	echo 'API Keys from the Database will be used'
fi




# handle user stuff
echo "---------------"
echo "Check if uid $UUID and $GUID exists..."
if [ "$(getent group| grep $GUID| wc -l)" -gt "0" ] ; then
	export GROUPNAME=$(getent group| grep $GUID | cut -d: -f1)
	echo "GUID $GUID exists with name $GROUPNAME"
else
	echo "GUID $GUID does not exist, creating $GROUPNAME"
	export GROUPNAME=eventula
	addgroup -g $GUID $GROUPNAME
fi

if id -u "$UUID" >/dev/null 2>&1; then
	export USERNAME=$(id -un "$UUID")
    echo "user $UUID exists with username $USERNAME"
else
 	echo "user $UUID does not exists, create eventula"
    adduser --disabled-password --uid $UUID --ingroup $GROUPNAME eventula
	export USERNAME=$(id -un "$UUID")
fi


if [ "$(id -nGz $USERNAME | tr '\0' '\n' | grep '^'${GROUPNAME}'$' | wc -l)" -gt "0" ] ; then
	echo "User $USERNAME is in group $GROUPNAME";
else
	echo "User $USERNAME is not in group $GROUPNAME , adding...";
	addgroup $USERNAME $GROUPNAME
fi


# Populate Storage Volume if Bind mount - Fix for Bind Mounts on Host system
if [ -z "$(ls -A $NGINX_DOCUMENT_ROOT/storage)" ]; then
	echo "---------------"
    echo "Storage on Bind mount is empty. Copying sample data ..."
 	cp -a /tmp/storage $NGINX_DOCUMENT_ROOT
fi

# APPEAR_DISABLE_CUSTOM_CSS_LINKING
if [ "$APPEAR_DISABLE_CUSTOM_CSS_LINKING" = 'false' ]; then

	echo "---------------"
    echo "CUSTOM_CSS_LINKING ..."

	mkdir -p $NGINX_DOCUMENT_ROOT/storage/user/scss
	chown -R $UUID:$GUID $NGINX_DOCUMENT_ROOT/storage/user/scss

	# Copy _user-override.scss and create symlink for it
	if [ ! -f "$NGINX_DOCUMENT_ROOT/storage/user/scss/_user-override.scss" ]; then
    	echo "_user-override.scss not available in storage/user/scss, copy default file"
		cp -rf $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/components/_user-override.scss $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-override.scss
		chown -R $UUID:$GUID $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-override.scss
	fi

	if [ -f "$NGINX_DOCUMENT_ROOT/storage/user/scss/_user-override.scss" ]; then
    	echo "_user-override.scss available in storage/user/scss, remove internal one and symlink to it"
		rm -rf $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/components/_user-override.scss
		ln -s  $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-override.scss $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/components/_user-override.scss
		chown -R -h $UUID:$GUID $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/components/_user-override.scss
	fi

	# Copy _user-variables.scss and create symlink for it
	if [ ! -f "$NGINX_DOCUMENT_ROOT/storage/user/scss/_user-variables.scss" ]; then
    	echo "_user-variables.scss not available in storage/user/scss, copy default file"
		cp -rf $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/modules/_user-variables.scss $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-variables.scss
		chown -R $UUID:$GUID $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-variables.scss
	fi

	if [ -f "$NGINX_DOCUMENT_ROOT/storage/user/scss/_user-variables.scss" ]; then
    	echo "_user-variables.scss available in storage/user/scss, remove internal one and symlink to it"
		rm -rf $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/modules/_user-variables.scss
		ln -s $NGINX_DOCUMENT_ROOT/storage/user/scss/_user-variables.scss $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/modules/_user-variables.scss 
		chown -R -h $UUID:$GUID $NGINX_DOCUMENT_ROOT/resources/assets/sass/stylesheets/app/modules/_user-variables.scss
	fi

fi

#permissions
if [[ $(stat -c "%u" $NGINX_DOCUMENT_ROOT/storage) != $UUID ]]; then
	echo "---------------"
    echo "Changing ownership of $NGINX_DOCUMENT_ROOT/storage to $UUID ..."
    chown -R $UUID:$GUID $NGINX_DOCUMENT_ROOT/storage
    chown -R -h $UUID:$GUID $NGINX_DOCUMENT_ROOT/storage
fi

# Make Symlink for images if it doesn't already exist
if [ ! -L "$NGINX_DOCUMENT_ROOT/public/storage" ]; then
	php artisan storage:link
fi


  

# Set Timezone
if [ "$TIMEZONE" != "UTC" ]; then
	cp /usr/share/zoneinfo/$TIMEZONE /etc/localtime
	echo "$TIMEZONE" >  /etc/timezone
fi


# Set users in configs
echo "---------------"
echo "replacing users in configs"
sed -i "s|user = .*|user = $UUID |g" /usr/local/etc/php-fpm.d/www.conf
sed -i "s|group = .*|group = $GUID |g" /usr/local/etc/php-fpm.d/www.conf
sed -i "s|%%USERNAME%%|$USERNAME|g" /etc/nginx/nginx.conf
sed -i "s|%%USERNAME%%|$USERNAME|g" /etc/nginx/nginx-ssl.conf
sed -i "s|%%USERNAME%%|$USERNAME|g" /etc/supervisor/conf.d/supervisord.conf

# set permissions
echo "---------------"
echo "set file permissions, this will take some time..."
if [[ "$UUID" != "82" || "$GUID" != "82" ]]; then
	echo "set src owner..."
	find $NGINX_DOCUMENT_ROOT ! -user $USERNAME -print0 | xargs -0 -r chown $UUID:$GUID
fi
echo "set file permissions..."
find $NGINX_DOCUMENT_ROOT -type f ! -perm 0664 -not -path "$NGINX_DOCUMENT_ROOT/node_modules/*" -print0 | xargs -0 -r chmod 664
echo "set folder permissions..."
find $NGINX_DOCUMENT_ROOT -type d ! -perm 0775 -not -path "$NGINX_DOCUMENT_ROOT/node_modules/*" -print0 | xargs -0 -r chmod 775
echo "set storage and cache permissions..."
chmod -R ug+rwx $NGINX_DOCUMENT_ROOT/storage $NGINX_DOCUMENT_ROOT/bootstrap/cache
echo "set nginx tmp permissions..."
mkdir -p /var/lib/nginx/tmp/client_body
chown -R $UUID:$GUID /var/lib/nginx/tmp

# handle URL overwrite
APP_URL="${APP_URL#http://}"
APP_URL="${APP_URL#https://}"

if { [ -n "$ENABLE_HTTPS" ] && [ "$ENABLE_HTTPS" = "true" ]; } || { [ -n "$FORCE_APP_HTTPS" ] && [ "$FORCE_APP_HTTPS" = "true" ]; }; then
  APP_URL="https://${APP_URL}"
else
  APP_URL="http://${APP_URL}"
fi
export APP_URL


# Database Wait check
echo "---------------"
echo "WAITING FOR $DB_HOST:$DB_PORT..."
/run/wait-for.sh $DB_HOST:$DB_PORT --timeout=30 --strict -- /run/start-supervisord.sh
