# BugShot API

## How to install

### 0. Prerequisites

1. PHP 8+ and extensions for it

		sudo apt install php libapache2-mod-php php-mbstring php-xmlrpc php-soap php-gd php-xml php-cli php-zip php-bcmath php-tokenizer php-json php-pear php-mysql php-curl

2. [Composer](https://getcomposer.org/)

	Good install instructions for Ubuntu: [link](https://www.digitalocean.com/community/tutorials/how-to-install-composer-on-ubuntu-20-04-quickstart).

### 1. Config .env

- Make a copy in the same place of `.env.example` with the name `.env`
- Run `"php artisan generate:key"` in the CLI, this will generate a key for the app in the `.env` file
- Modify the `APP_(*_)URL` with the correct links so that the frontend and other services can connect to the app
- Set up the database and other services credentials

### 2. Install composer pacakges

- Run `"composer install"` if composer is installed globaly

### 3. Database

- Do a `"php artisan migrations"`. 

	If the command does not work and says something on the lines of `"could not find driver..."` make sure there is a php driver installed on the machine like `php_mysql` for a mysql database of the equivalent  for waht database is necessary. 

		install command example:
		sudo apt install php-mysql

- Run the seeders `"php artisan db:seed"`
- Run `"php artisan usersettings:fresh"` to make sure all the user settings that need to be set are set at the start (some users have been added using seeders, if there are no users added this way you can skip this step).

### 4. Misc

- `"php artisan storage:link"` to make the apropriate links for storage


## How to restart BugShot worker in case it crashes

The BugShot workers are managed by [Supervisor](http://supervisord.org/) and their lifespan are set to 1 hour, after that they stop and will be automatically started again by Supervisor. The config is found in the root api directory on the XX Server in `supervisor.conf`; the config for a specific worker can be found under `[program:*worker name*]`.

### 1. Connect to the server using ssh

    Hostname, Username and Password found in the BugShot KeePass -> Aplikation -> SSH

### 2. Navigate to the api directory

    cd api

Normally you only need to provide the relative path and you should arrive at `/www/htdocs/xx/api` or something very similar.

### 3. Check [Supervisor](http://supervisord.org/)

-   first check that `supervisor.conf` is present in the current directory (`ls -l`)
-   run `supervisorctl status` to get the status of workers. Example output:

```
ssh-xx@dd52600:/www/htdocs/xx/api$ supervisorctl status
api-dev-worker:api-dev-worker_00           RUNNING   pid 3325937, uptime 0:03:20
api-live-worker:api-live-worker_00         RUNNING   pid 3227694, uptime 0:21:23
api-live-worker:api-live-worker_01         RUNNING   pid 3227830, uptime 0:21:22
api-staging-worker:api-staging-worker_00   RUNNING   pid 3227695, uptime 0:21:23
ssh-xx@dd52600:/www/htdocs/xx/api$
```

-   in case the output is something like this:

```
ssh-xx@dd52600:/www/htdocs/xx/api$ supervisorctl status
unix:///tmp/supervisor.sock no such file
ssh-xx@dd52600:/www/htdocs/xx/api$
```

you need to run `supervisord` and after run the `supervisorctl status` to check if the service service and workers started

-   if the output of the `supervisorctl status` is empty try to run `supervisorctl restart`
