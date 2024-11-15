
Getting Started
==================================================

To develop eventula, you should use the Makefile the repository.


Prerequisites
..............

- Linux or inside WSL (please clone your repo already in WSL)
- Docker v17+
- Docker-compose v1.18+
- API Keys for one of the Payment Providers
- you need ``make`` installed
- your default user needs access to the docker daemon (https://docs.docker.com/engine/install/linux-postinstall/)


Initial setup
...............................
This method uses docker-compose to build the image and database from the source code instead of pulling them from docker hub. 

You need the whole source code cloned for running this! 

Its worth checking out the Makefile, there are many useful commands implemented that you can use to make your life easier. 

This Makefile will be documented fully soon.

    .. warning::

        Do not use your root user to run this commands or clone the repository! 
        
        Do not use any commands from the make file that include ``--user 82:82`` or ``--user 0`` (like ``npm-install-gh`` or ``composer-install``). 
        
        **In both cases you will have permission problems!**


1. Clone Repository
^^^^^^^^^^^^^^^^^^^^^^^^^

Run ``https://github.com/Lan2Play/eventula-manager.git`` and change into it.

2. Run initial setup
^^^^^^^^^^^^^^^^^^^^^^^^^
Run the developmet stack initially 

``make``

you should have a fully working development environment after that. The container is ran interactiveley, so you can let it running or exit it with STRG+C

3. skip eventula installation page (optional)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
to skip the installation page of eventula, you can run 

``make set-installed``
and 
``make generate-testuser``

The login credentials are mentioned in the :ref:`dev/getting_started:generate testusers` section below.


Running after initial setup
...............................


Run the development stack after the first run
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Run the developmet stack after the initial setup interactiveley again. This will not build the container image again, since you only need to do that if you change something on the container.

``make interactive``

Run the development stack after the first run with docker rebuild
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Run the developmet stack after the initial setup detached again. This will rebuild the container image before running.

``make dev``


Container Environment
...............................
The makefile will copy the ``.env.example`` from the root of the repository to ``src/.env``. You can customize ``src/.env`` to your needs if you need something. 
The .env file should have all Nessecary comments to understand the purpose of the variables.

.. warning::

    Keep in mind that the passwords that are predifened in the env example are also required for some of the make commands with hardcoded passwords, so dont change these!


Nessecary/useful commands for development
........................................................................

All the commands should be run when the stack is running (other than you know what you are doing). There are some more commands in the Makefile, so you might take a look.

Stop
^^^^^^^^^^^^^^^^^^^^^^^^^

Stop the stack.

``make stop``


Purge all "temporary" local filed
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

To rebuild everything (if you for example switch branches), you can run

``make purge-all``

after that you have to run the initial setup again.


Migrate & Seed Database
^^^^^^^^^^^^^^^^^^^^^^^^^

If you make changes to the Database files, you can run Database migrations or seeding with the following commands.

``make database-migrate``
``make database-seed``


Rebuild NPM Dependencies / Stylesheets
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you make changes to the style sheets or npm Dependencies, you can run 

``make npm-install-dev``

Run Composer install 
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you make changes to the composer Dependencies, you can run 

``make composer-install-dev``

Add Composer dependency 
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to add a composer Dependency, you can run 

``make composer-add-dep module=module/namehere``

Add Composer dev dependency 
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to add a composer dev Dependency, you can run 

``make composer-add-dep-dev module=module/namehere``

clear the cache
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to clear laravels cache, you can run 

``make purge-cache``

generate testusers
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to generate testusers, you can run

``make generate-testuser``

this will add 50 testusers with the following credentials (replace ``%N%`` with a number from 1-50):

Mail: ``test%N%@test.de``
Password: ``test%N%test%N%``

and an additional Administrator user:

Mail: ``Administrator1@Administrator.de``
Password: ``Administrator1Administrator1``


run database command / sql command
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to run a sql query, you can simply use:

``make database-command command="sqlcommandhere"``

run command in the dev container
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to run a command in the container, you can simply use:

``make command command="commandhere"``

recreate the database from scratch
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to recreate the databse, you can simply use:

``make database-renew``

regenerate PHPDoc (intelliphense) and PHPStorm Definitions
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
If you want to recreate the PHPDoc (intelliphense) and PHPStorm Definitions, you can simply use:

``make regenerate-idehelper``
