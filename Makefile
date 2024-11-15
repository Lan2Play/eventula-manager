# Initialize variables
ifeq ($(OS),Windows_NT)
currentDir = $(patsubst %/,%, $(lastword $(subst /host, ,$(shell wsl wslpath -u $(strip $(dir $(realpath $(lastword $(MAKEFILE_LIST)))))))))
userId = $(shell wsl id -u)
groupId = $(shell wsl id -g)
else
currentDir = $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
userId = $(shell id -u)
groupId = $(shell id -g)
endif

ifeq (userId,0)
userId = 1000
groupId = 1000
endif

user = --user $(userId):$(groupId)

ifeq ($(OS),Windows_NT)
  DOCKER_COMPOSE=docker compose
else
ifneq ($(shell docker compose version 2>/dev/null),)
  DOCKER_COMPOSE=docker compose
else
  DOCKER_COMPOSE=docker-compose
endif
endif

# Run local dev
start-local-dev: env-file-dev docker-lan app-build-clean-dev logs

switch-database: purge-containers dev-database wait-mysql database-import stop interactive

dev:
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml up -d --build

dev-database:
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml up -d eventula_manager_database

# Debug
interactive:
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml up

# Stop all Containers
stop:
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml stop ||: true

# Build from clean
app-build-clean: folder-structure-prd layout-images-prd app-build-dep generate-key-prd dev wait-mysql database-migrate database-seed stop

# Build dev from clean
app-build-clean-dev: folder-structure-dev layout-images-dev app-build-dep-dev purge-cache generate-key-dev dev #wait-mysql stop

# Build Dependencies
app-build-dep: composer-install npm-install mix

# Build Dev App & Dependencies
app-build-dep-dev: composer-install-dev npm-install-dev mix-dev

# Regenerate Idehelper (Update PHPdoc and PHPStorm definitions)
regenerate-idehelper: clear-config-cache idehelper-generate idehelper-meta idehelper-models

# Make Documentation
docs-html:
	docker run --rm -v $(currentDir)/docs:/docs lan2play/docker-sphinxbuild:latest

###########
# HELPERS #
###########

docker-lan:
ifeq ($(OS),Windows_NT)
#not tested!
ifeq ($(shell powershell -Command "docker network ls --filter NAME=lan | Measure-Object -Line | Select-Object -ExpandProperty Lines"),1)
	docker network create lan
endif
else
ifeq ($(shell docker network ls --filter=NAME=lan | wc -l),1)
	docker network create lan
endif
endif

local-prd-build-up:
	$(DOCKER_COMPOSE) -f docker-compose-local-prd.yml up --build

local-prd-up:
	$(DOCKER_COMPOSE) -f docker-compose-local-prd.yml up

local-prd-stop:
	$(DOCKER_COMPOSE) -f docker-compose-local-prd.yml stop

# Make .env
logs:
	$(DOCKER_COMPOSE) logs -f

# Make .env
env-file-dev:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir):/localdir \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	[ ! -f /localdir/src/.env ] && cp /localdir/.env.example /localdir/src/.env && sed -i \"s#UUID=82#UUID=$(userId)#g\" /localdir/src/.env && sed -i \"s#GUID=82#GUID=$(groupId)# g\" /localdir/src/.env; exit 0"

# Make .env
env-file-prd:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir):/localdir \
    --user 82:82 php:8-fpm-alpine /bin/sh -c " \
	[ ! -f /localdir/src/.env ] && cp /localdir/.env.example /localdir/src/.env; exit 0"

# Make blank .env
env-file-blank:
	touch src/.env
	# echo "APP_KEY=" >> src/.env

# Move default images to Storage
layout-images-prd:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/app \
    --user 82:82 php:8-fpm-alpine /bin/sh -c " \
	cp -r /app/resources/assets/images/* /app/storage/app/public/images/main/ && \
	mv /app/storage/app/public/images/main/shop/* /app/storage/app/public/images/shop/"

# Move default images to Storage
layout-images-dev:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/app \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	cp -r /app/resources/assets/images/* /app/storage/app/public/images/main/ && \
	mv /app/storage/app/public/images/main/shop/* /app/storage/app/public/images/shop/"

# Create Symlink for Storage
symlink:
	docker exec eventula_manager_app php artisan storage:link

# Create & Update the Database
database-migrate:
	docker exec eventula_manager_app php artisan migrate

# recreate & update the Database
database-migrate-refresh:
	docker exec eventula_manager_app php artisan migrate:refresh

# Seed the Database
database-seed:
	docker exec eventula_manager_app php artisan db:seed

# Rollback last Database Migration
database-rollback:
	docker exec eventula_manager_app php artisan migrate:rollback

# Generates PHPDoc for Laravel Facades (nessecary for intelliphense)
idehelper-generate:
	docker exec eventula_manager_app php artisan ide-helper:generate

# Generates PhpStorm Meta file 
idehelper-meta:
	docker exec eventula_manager_app php artisan ide-helper:meta

# Generates PHPDocs for models (nessecary for intelliphense)
idehelper-models:
	docker exec eventula_manager_app php artisan ide-helper:models --nowrite

# show newly generated Application Key
generate-key-show-newkey:
	docker run --rm composer:latest /bin/bash -c "echo 'generating key..' && composer create-project laravel/laravel example-app >/dev/null 2>/dev/null && cd example-app && php artisan key:generate >/dev/null 2>/dev/null && cat .env | grep APP_KEY=b"

# Generate Application key
generate-key-prd:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/app \
    --user 82:82 -e DB_CONNECTION=sqlite php:8-fpm-alpine /bin/sh -c "touch /app/database/database.sqlite; cd /app && php artisan key:generate; rm -rf /app/database/database.sqlite"

# Generate Application key
generate-key-dev:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/app \
    $(user) -e DB_CONNECTION=sqlite php:8-fpm-alpine /bin/sh -c "touch /app/database/database.sqlite; cd /app && php artisan key:generate; rm -rf /app/database/database.sqlite"

# Generate Settings - This will erase your current settings!
generate-settings:
	docker exec eventula_manager_app php artisan db:seed --class=SettingsTableSeeder

# Generate Appearance - This will erase your current settings!
generate-appearance:
	docker exec eventula_manager_app php artisan db:seed --class=AppearanceTableSeeder

# Generate Images - This will erase your current settings!
generate-images:
	docker exec eventula_manager_app php artisan db:seed --class=SliderImageTableSeeder

# Generate testusers - This will spam 50 testuser to the Database!
generate-testuser:
	docker exec eventula_manager_app php artisan db:seed --class=TestUserSeeder

# Generate event - This will generate a sample event!
generate-event:
	docker exec eventula_manager_app php artisan db:seed --class=EventsSeeder

# Generate test cs2 matchmaking match with a demo - This will generate a sample cs2 matchmaking match with a demo (needs the generate-games command before running)! example: make generate-cs2mm playeroneid=useridhere playertwoid=useridhere democount=democounthere status=statushere
generate-cs2mm:
	docker exec -e playeroneid=$(playeroneid) -e playertwoid=$(playertwoid) -e democount=$(democount) -e status=$(status) eventula_manager_app php artisan db:seed --class=TestCs2MatchMakingSeeder

# Generate demo(s) into existing challonge matches  (needs the generate-games command before running)! example: make generate-demos-tournament democount=democounthere challongematchid=challongematchidhere
generate-demos-tournament:
	docker exec -e democount=$(democount) -e challongematchid=$(challongematchid) eventula_manager_app php artisan db:seed --class=TestTournamentDemoSeeder

# Generate event - This will generate a sample event!
generate-games:
	docker exec eventula_manager_app php artisan db:seed --class=GamesTableSeeder

# Generate requireddatabase - This will erase your current settings!
generate-requireddatabase:
	docker exec eventula_manager_app php artisan db:seed --class=RequiredDatabaseSeeder --force

# execute command make command command=sqlcommandhere
command:
	docker exec eventula_manager_app $(command)


# clear views
clear-views:
	docker exec eventula_manager_app php artisan view:clear

# clear cache
clear-cache:
	docker exec eventula_manager_app php artisan cache:clear

# clear configuration cache
clear-config-cache:
	docker exec eventula_manager_app php artisan config:cache


# Create Default Folder structure
folder-structure-prd:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/src \
    --user 82:82 php:8-fpm-alpine /bin/sh -c " \
	mkdir -p /src/storage/app/public/images/gallery/ && \
	mkdir -p /src/storage/app/public/images/events/ && \
	mkdir -p /src/storage/app/public/images/venues/ && \
	mkdir -p /src/storage/app/public/images/main/ && \
	mkdir -p /src/storage/app/public/images/shop/ && \
	mkdir -p /src/storage/app/public/images/avatars/ && \
	mkdir -p /src/storage/app/public/attachments/help/"

folder-structure-dev:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/src \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	mkdir -p /src/storage/app/public/images/gallery/ && \
	mkdir -p /src/storage/app/public/images/events/ && \
	mkdir -p /src/storage/app/public/images/venues/ && \
	mkdir -p /src/storage/app/public/images/main/ && \
	mkdir -p /src/storage/app/public/images/shop/ && \
	mkdir -p /src/storage/app/public/images/avatars/ && \
	mkdir -p /src/storage/app/public/attachments/help/"

# Create SSL Keypair for Development
ssl-keygen:
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout resources/certs/eventula_manager.key -out resources/certs/eventula_manager.crt

# Install PHP Dependencies via Composer
composer-install:
	docker run --rm --name compose-maintainence --interactive \
    --volume $(currentDir)/src:/app \
    --user 82:82 \
    composer:latest composer install --ignore-platform-reqs --no-scripts

# Install Dev PHP Dependencies via Composer
composer-install-dev:
	docker run --rm --name compose-maintainence-dev --interactive \
    -v $(currentDir)/src:/app \
    $(user) \
    composer:latest composer install --ignore-platform-reqs --no-scripts --dev

# Update Dev PHP Dependencies via Composer
composer-update:
	docker run --rm --name compose-maintainence-update --interactive \
    --volume $(currentDir)/src:/app \
    $(user) \
    composer:latest composer update --ignore-platform-reqs --no-scripts

# list Composer outdated direct
composer-outdated-direct:
	docker run --rm --name compose-maintainence-update --interactive \
    --volume $(currentDir)/src:/app \
    $(user) \
    composer:latest composer outdated -D --ignore-platform-reqs

# list Composer outdated
composer-outdated:
	docker run --rm --name compose-maintainence-update --interactive \
    --volume $(currentDir)/src:/app \
    $(user) \
    composer:latest composer outdated --ignore-platform-reqs

# add PHP Dependencies via Composer - usage make composer-add-dep module=module/namehere
composer-add-dep:
	docker run --rm --name compose-maintainence-update --interactive \
    --volume $(currentDir)/src:/app \
    $(user) \
    composer:latest composer require $(module) --ignore-platform-reqs --no-scripts

# add Dev PHP Dependencies via Composer - usage make composer-add-dep module=module/namehere
composer-add-dep-dev:
	docker run --rm --name compose-maintainence-update --interactive \
    --volume $(currentDir)/src:/app \
    $(user) \
    composer:latest composer require $(module) --ignore-platform-reqs --no-scripts --dev

# Install JS Dependencies via NPM
npm-install:
	docker run --rm --name js-maintainence --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
    --user 82:82 \
	node:22.9 /bin/bash -ci "npm install --no-audit && npm run production"

# Install PRD JS Dependencies via NPM locally
npm-install-local:
	docker run --rm --name js-maintainence-dev --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm install --no-audit && npm run production"

# Install JS Dependencies via NPM
npm-install-gh:
	docker run --rm --name js-maintainence --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
    --user 0 \
	node:22.9 /bin/bash -ci "npm install --no-audit && npm run production && chown -R $(userId):$(groupId) /usr/src/app"

# Install Dev JS Dependencies via NPM
npm-install-dev:
	docker run --rm --name js-maintainence-dev --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm install --no-audit && npm run dev"

#list npm package - usage make npm-ls module=module
npm-ls:
	docker run --rm --name js-maintainence-list --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm ls $(module)"

#update npm packages - usage make npm-update
npm-update:
	docker run --rm --name js-maintainence-list --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm update"

#audit npm packages - usage make npm-audit
npm-audit:
	docker run --rm --name js-maintainence-list --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm audit"

#audit fix npm packages - usage make npm-audit-fix
npm-audit-fix:
	docker run --rm --name js-maintainence-list --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm audit fix"


#list outdated npm packages
npm-outdated:
	docker run --rm --name js-maintainence-outdated --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm outdated"

#rebuild node
npm-rebuild:
	docker run --rm --name js-maintainence-outdated --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm rebuild"

# npm mix Runner
mix:
	docker run --rm --name js-maintainence-dev --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
    --user 82:82 \
	node:22.9 /bin/bash -ci "npm run production"

mix-dev:
	docker run --rm --name js-maintainence-dev --interactive \
	-v $(currentDir)/src:/usr/src/app \
	-w /usr/src/app \
	$(user) \
	node:22.9 /bin/bash -ci "npm run development"

# Purge Containers
purge-containers:
ifeq ($(OS),Windows_NT)
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml stop ||: true
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml rm -vf ||: true
	docker rm eventula_manager_app ||: true
	docker rm eventula_manager_database ||: true
	docker volume rm eventula_manager_database ||: true
	docker volume rm eventula_manager_storage ||: true
else
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml -p eventula_manager stop || true
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml -p eventula_manager rm -vf || true
	docker rm eventula_manager_app || true
	docker rm eventula_manager_database || true
	docker volume rm eventula_manager_database || true
	docker volume rm eventula_manager_storage || true
endif

# Purge Caches
purge-cache:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/src \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	rm -rf /src/storage/framework/cache/* && \
	rm -rf /src/storage/framework/views/* && \
	rm -rf /src/storage/framework/sessions/* && \
	rm -rf /src/bootstrap/cache/* && \
	rm -rf /src/storage/debugbar/* "

# Purge Caches
purge-files:
	docker run --rm --name compkeygen --interactive \
	-v $(currentDir)/src:/src \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	rm -rf /src/vendor/ ; \
	rm -rf /src/node_modules/ ; \
	rm -rf /src/public/css/* ; \
	mkdir -p /src/public/css/font; \
	mkdir -p /src/public/css/images; \
	touch /src/public/css/font/.gitkeep; \
	touch /src/public/css/images/.gitkeep; \
	rm -rf /src/storage/app/public/images/gallery ; \
	rm -rf /src/storage/app/public/images/events ; \
	rm -rf /src/storage/app/public/images/avatars ; \
	rm -rf /src/storage/app/public/images/venues ; \
	rm -rf /src/storage/app/public/images/main ; \
	rm -rf /src/storage/user/scss/*.css ; \
	rm -rf /src/storage/user/scss/*.scss ; \
	rm -rf /src/storage/logs/* ; \
	rm -rf /src/public/storage || true"


# execute mysql upgrade after upgrading db container usage make database-upgrade sqlrootpw=rootpasswordhere
database-upgrade:
	docker exec -i eventula_manager_database /bin/bash -c 'mysql_upgrade -uroot -p$(sqlrootpw)'

# execute mysql command usage make database-command command=sqlcommandhere
database-command:
ifeq ($(OS),Windows_NT)
	powershell.exe -Command "echo \"use eventula_manager_database; $(command)\"| docker exec -i eventula_manager_database mysql -u eventula_manager -p'password'"
else
	echo "use eventula_manager_database; $(command)" | docker exec -i eventula_manager_database mysql -u eventula_manager -p'password'
endif

# import mysql database usage make database-command dbfile=dbfile.sql
ifndef dbfile
override dbfile = dbfile.sql
endif
database-import:
	docker exec -i eventula_manager_database mysql -u eventula_manager -p'password' eventula_manager_database < $(dbfile)

# drops the database
database-drop:
	echo "DROP DATABASE eventula_manager_database;" | docker exec -i eventula_manager_database mysql -u eventula_manager -p'password'

# creates the database
database-create:
	echo "CREATE DATABASE eventula_manager_database;" | docker exec -i eventula_manager_database mysql -u eventula_manager -p'password'

# creates the database
database-renew:	database-drop database-create database-migrate database-seed generate-requireddatabase

#show foreign keys usage make database-show-foreign table=tablename
database-show-foreign:
	echo "SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = 'eventula_manager_database' AND REFERENCED_TABLE_NAME = '$(table)';" | docker exec -i eventula_manager_database mysql -u eventula_manager -p'password'

# get @lang from blade usage make get-blade-lang blade=pathtoblade prefix=langprefix
get-lang-blade:
	docker run --rm --name mysqlwaiter --interactive --network="lan" \
	-e WAIT_HOSTS="eventula_manager_database:3306" \
    $(user) php:8-fpm-alpine /bin/sh -c " \
	cat $(blade) | grep -o \"'$(prefix)\..*'\" | sed \"s|'||g\" | sort | uniq | sed \"s|.*\.|'|g\" | sed -e \"s/$$/' => '',/\""


# set installed in database
set-installed:
	make database-command command="update settings set value=1 where setting='installed';"

# set not installed in database
set-not-installed:
	make database-command command="update settings set value=0 where setting='installed';"

get-wait:
ifneq ("$(wildcard $(currentDir)/resources/wait)","")
	echo "wait exists"
else
	docker run --rm --name mysqlwaiter --interactive --network="lan" \
	-e WAIT_HOSTS="eventula_manager_database:3306" \
	-v $(currentDir):/usr/src/app \
	$(user) php:8-fpm-alpine /bin/sh -c " \
	wget -O /usr/src/app/resources/wait https://github.com/ufoscout/docker-compose-wait/releases/latest/download/wait && \
	chmod +x /usr/src/app/resources/wait"
endif

# Wait for mysql to initialize
wait-mysql: get-wait
	docker run --rm --name mysqlwaiter --interactive --network="lan" \
	-e WAIT_HOSTS="eventula_manager_database:3306" \
	-e WAIT_AFTER="45" \
	-v $(currentDir):/usr/src/app \
	$(user) php:8-fpm-alpine /bin/sh -c " \
	/usr/src/app/resources/wait"



###############
# DANGER ZONE #
###############
# Clean ALL! DANGEROUS!
purge-all:
	echo 'This is dangerous!'
	echo 'This will totally remove all data and information stored in your app!'
	@echo -n "Are you sure? [y/N] " && read ans && [ $${ans:-N} = y ]
	make purge-all-force

purge-all-force: stop purge-containers purge-cache purge-files