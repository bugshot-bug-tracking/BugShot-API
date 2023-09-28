# BugShot API

## How to restart BugShot worker in case it crashes

The BugShot workers are managed by [Supervisor](http://supervisord.org/) and their lifespan are set to 1 hour, after that they stop and will be automatically started again by Supervisor. The config is found in the root api directory on the KasServer in `supervisor.conf`; the config for a specific worker can be found under `[program:*worker name*]`.

### 1. Connect to the server using ssh

    Hostname, Username and Password found in the BugShot KeePass -> Aplikation -> SSH

### 2. Navigate to the api directory

    cd api

Normally you only need to provide the relative path and you should arrive at `/www/htdocs/w01a172d/api` or something very similar.

### 3. Check [Supervisor](http://supervisord.org/)

-   first check that `supervisor.conf` is present in the current directory (`ls -l`)
-   run `supervisorctl status` to get the status of workers. Example output:

```
ssh-w01a172d@dd52600:/www/htdocs/w01a172d/api$ supervisorctl status
api-dev-worker:api-dev-worker_00           RUNNING   pid 3325937, uptime 0:03:20
api-live-worker:api-live-worker_00         RUNNING   pid 3227694, uptime 0:21:23
api-live-worker:api-live-worker_01         RUNNING   pid 3227830, uptime 0:21:22
api-staging-worker:api-staging-worker_00   RUNNING   pid 3227695, uptime 0:21:23
ssh-w01a172d@dd52600:/www/htdocs/w01a172d/api$
```

-   in case the output is something like this:

```
ssh-w01a172d@dd52600:/www/htdocs/w01a172d/api$ supervisorctl status
unix:///tmp/supervisor.sock no such file
ssh-w01a172d@dd52600:/www/htdocs/w01a172d/api$
```

you need to run `supervisord` and after run the `supervisorctl status` to check if the service service and workers started

-   if the output of the `supervisorctl status` is empty try to run `supervisorctl restart`
