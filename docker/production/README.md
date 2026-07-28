# AureusERP — Production Docker Image

A single-container, production-ready Docker image for
[AureusERP](https://github.com/aureuserp/aureuserp). It bundles the application,
MySQL, PHP-FPM, Nginx and Supervisor — everything needed to run the ERP with one
`docker run`.

AureusERP is **fully installed at build time** (migrations, seeders, roles &
permissions, admin user), so the container boots ready to use.

> This is the **production** image. For local development use Laravel Sail via
> the `docker-compose.yml` at the repository root.

## Contents

- [What's inside](#whats-inside)
- [Repository layout](#repository-layout)
- [Quick start](#quick-start)
- [Building the image](#building-the-image)
- [Running the container](#running-the-container)
- [Access & default credentials](#access--default-credentials)
- [Environment variables](#environment-variables)
- [HTTP vs HTTPS](#http-vs-https)
- [Database modes](#database-modes)
- [Persistence](#persistence)
- [How it works](#how-it-works)
- [Multi-architecture](#multi-architecture)
- [Upgrading](#upgrading)
- [Common commands](#common-commands)
- [Troubleshooting](#troubleshooting)
- [Notes & limitations](#notes--limitations)
- [Support](#support)

## What's inside

| Component | Detail |
|---|---|
| Base OS | Ubuntu 24.04 |
| Web server | Nginx (port 80) |
| PHP | 8.4 FPM — bcmath, curl, exif, gd, gmp, intl, mbstring, mysql, soap, xml, zip, imagick |
| Database | MySQL 8.0 (internal, pre-installed) |
| Process manager | Supervisor — `mysql` · `php-fpm` · `nginx` · queue worker · scheduler |
| Application path | `/var/www/aureuserp` |

The image runs in one of two database modes:

| Mode | When | Behaviour |
|---|---|---|
| Internal MySQL | `DB_HOST` unset / `127.0.0.1` / `localhost` (default) | MySQL runs inside the container |
| External MySQL | `DB_HOST` set to another address | Internal MySQL stays off; the app uses the external server |

## Repository layout

```
docker/production/
├── Dockerfile          # single-stage image definition
├── .dockerignore       # build-context exclusions
├── build-install.sh    # build-time install — migrates, seeds, bakes the MySQL data dir
├── entrypoint.sh       # runtime — applies env overrides, refreshes caches, starts Supervisor
├── mysql-init.sql      # creates the internal `aureus` database and user
├── nginx.conf          # virtual host
├── php.ini             # PHP / OPcache tuning
├── php-fpm.conf        # PHP-FPM pool
├── supervisord.conf    # process definitions
└── README.md           # this file
```

The application source is fetched with `git clone` during the build, so the
image always builds **committed** code. Releases are automated via
`.github/workflows/docker_publish.yml` on `v*` tags.

## Quick start

Pull and run the published image:

```bash
docker pull webkul/aureuserp:latest

docker run -d --name aureuserp -p 80:80 \
  -v aureus-mysql:/var/lib/mysql \
  -v aureus-storage:/var/www/aureuserp/storage \
  webkul/aureuserp:latest
```

Then open <http://localhost>. To use a different host port, change `-p`, e.g.
`-p 8080:80` → <http://localhost:8080>.

## Building the image

The build context is the `docker/production/` directory. Build from the
repository root:

```bash
# default — clones aureuserp/aureuserp @ master
docker build -t aureuserp:latest docker/production

# a specific branch or tag
docker build -t aureuserp:1.0.0 \
  --build-arg APP_REF=v1.0.0 \
  docker/production
```

The build clones the repo, installs Composer dependencies, compiles front-end
assets, and installs the ERP — it takes several minutes.

### Build arguments

| Argument | Default | Description |
|---|---|---|
| `APP_REF` | `master` | Branch or tag of AureusERP to clone |
| `REPO_URL` | `https://github.com/aureuserp/aureuserp.git` | Repository to clone |
| `PHP_VERSION` | `8.4` | PHP version |
| `NODE_VERSION` | `22` | Node.js version (used only to compile assets) |
| `ADMIN_NAME` | `Administrator` | Admin account name created at install |
| `ADMIN_EMAIL` | `admin@example.com` | Admin account email created at install |
| `ADMIN_PASSWORD` | `password` | Admin account password created at install |

## Running the container

```bash
# basic
docker run -d --name aureuserp -p 80:80 aureuserp:latest

# different host port
docker run -d --name aureuserp -p 8080:80 aureuserp:latest

# foreground (stream logs, no -d)
docker run --name aureuserp -p 80:80 aureuserp:latest

# with environment overrides
docker run -d --name aureuserp -p 80:80 \
  -e APP_URL=https://erp.example.com \
  -e APP_NAME="My Company ERP" \
  -e APP_TIMEZONE=Asia/Kolkata \
  aureuserp:latest

# with persistent named volumes (recommended)
docker run -d --name aureuserp -p 80:80 \
  -v aureus-mysql:/var/lib/mysql \
  -v aureus-storage:/var/www/aureuserp/storage \
  aureuserp:latest
```

## Access & default credentials

| | |
|---|---|
| Application | <http://localhost> |
| Admin panel | <http://localhost/admin> |
| Default admin | `admin@example.com` / `password` |

**Change the admin password immediately** after the first login. Set custom
credentials at build time with the `ADMIN_*` build arguments.

## Environment variables

### Build arguments

See [Build arguments](#build-arguments) above — `APP_REF`, `REPO_URL`,
`PHP_VERSION`, `NODE_VERSION`, `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`.

### Runtime variables

| Variable | Default | Description |
|---|---|---|
| `APP_ENV` | `production` | `production` forces URLs to HTTPS; `local` serves over HTTP — see [HTTP vs HTTPS](#http-vs-https) |
| `APP_DEBUG` | `false` | Detailed error pages when `true` — keep `false` in production |
| `APP_NAME` | `AureusERP` | Application name |
| `APP_URL` | `http://localhost` | Public base URL |
| `APP_KEY` | _baked_ | Encryption key — override to pin a stable key |
| `APP_LOCALE` | `en` | Default locale |
| `APP_CURRENCY` | `USD` | Default currency |
| `APP_TIMEZONE` | `UTC` | Application timezone |
| `DB_HOST` | `127.0.0.1` | Database host — see [Database modes](#database-modes) |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | `aureus` | Database name |
| `DB_USERNAME` | `aureus` | Database user |
| `DB_PASSWORD` | `aureus` | Database password |

## HTTP vs HTTPS

AureusERP forces every generated URL to `https` when `APP_ENV=production` (the
default — correct for a live site behind TLS). For **local testing over plain
HTTP**, run with `APP_ENV=local`:

```bash
docker run -d --name aureuserp -p 8080:80 \
  -e APP_ENV=local \
  -e APP_URL=http://localhost:8080 \
  aureuserp:latest
```

The image has no built-in TLS — terminate HTTPS at a reverse proxy or load
balancer in front of the container.

## Database modes

### Internal MySQL (default)

MySQL runs inside the container against a data directory baked at build time.
Nothing to configure — just run the image.

### External MySQL

Set `DB_HOST` to a non-local address; the internal MySQL then stays off and the
entrypoint waits up to 60 s for the external server.

```bash
docker run -d --name aureuserp -p 80:80 \
  -v aureus-storage:/var/www/aureuserp/storage \
  -e DB_HOST=db.example.com \
  -e DB_PORT=3306 \
  -e DB_DATABASE=aureus \
  -e DB_USERNAME=aureus \
  -e DB_PASSWORD=a-strong-password \
  -e APP_URL=https://erp.example.com \
  aureuserp:latest
```

Create the database and user on the external server first:

```sql
CREATE DATABASE aureus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aureus'@'%' IDENTIFIED BY 'a-strong-password';
GRANT ALL PRIVILEGES ON aureus.* TO 'aureus'@'%';
FLUSH PRIVILEGES;
```

An external database is **not pre-installed**. Run the installer against it once
(`APP_ENV` is overridden so the production guard does not block the migrations):

```bash
docker exec -e APP_ENV=local aureuserp \
  php artisan erp:install --force --no-interaction \
  --admin-name=Administrator \
  --admin-email=admin@example.com \
  --admin-password=password
```

## Persistence

The image declares **no** `VOLUME` directives — persistence is opt-in. Use
**named volumes** (named volumes receive a copy of the image's baked content on
first run; bind mounts do not, and an empty bind mount would shadow the
installed data).

| Volume | Container path | Purpose |
|---|---|---|
| `aureus-mysql` | `/var/lib/mysql` | Database files |
| `aureus-storage` | `/var/www/aureuserp/storage` | Uploads, logs, sessions, app state |

Without volumes the container is ephemeral — all data is lost on `docker rm`.

## How it works

**Build time** (`build-install.sh`): MySQL is started temporarily, the database
and user are created, `php artisan erp:install` runs migrations + seeders +
roles + the admin user, then MySQL is shut down. The populated `/var/lib/mysql`
is baked into the image, so the container boots instantly with no setup.

**Run time** (`entrypoint.sh`):

1. Detects internal vs. external database mode from `DB_HOST`.
2. Applies environment overrides (`APP_*`, `DB_*`) to `.env`.
3. In external mode, waits for the external database.
4. Caches config and views; leaves routes dynamic (AureusERP registers plugin
   routes from the database, so route caching is intentionally not used).
5. Hands off to Supervisor, which starts `mysql`, `php-fpm`, `nginx`, the queue
   worker and the scheduler.

## Multi-architecture

The image runs on both **`amd64`** and **`arm64`** — every base image and
package source supports both. Published images on Docker Hub
(`webkul/aureuserp`) are multi-arch, so `docker pull` / `docker run` selects the
right architecture automatically.

To build a multi-arch image yourself:

```bash
docker buildx build --platform linux/amd64,linux/arm64 \
  -t webkul/aureuserp:latest --push docker/production
```

A multi-arch image must be pushed to a registry — the local Docker daemon cannot
hold both architectures under one tag.

## Upgrading

The image is immutable (`opcache.validate_timestamps=0`), so a new version means
a new image:

```bash
docker pull webkul/aureuserp:latest        # or rebuild locally
docker stop aureuserp && docker rm aureuserp
docker run -d --name aureuserp -p 80:80 \
  -v aureus-mysql:/var/lib/mysql \
  -v aureus-storage:/var/www/aureuserp/storage \
  webkul/aureuserp:latest
```

When the `aureus-mysql` volume is reused, apply any new migrations:

```bash
docker exec aureuserp php artisan migrate --force
```

Back up the database volume before upgrading:

```bash
docker run --rm -v aureus-mysql:/data -v "$(pwd)":/backup alpine \
  tar czf /backup/aureus-mysql-backup.tar.gz /data
```

## Common commands

```bash
# logs
docker logs aureuserp
docker logs -f --tail 100 aureuserp

# shell
docker exec -it aureuserp bash

# service status / restart
docker exec aureuserp supervisorctl status
docker exec aureuserp supervisorctl restart nginx

# artisan
docker exec aureuserp php artisan about
docker exec aureuserp php artisan migrate --force

# stop / remove
docker stop aureuserp
docker rm aureuserp

# wipe persistent data
docker volume rm aureus-mysql aureus-storage
```

## Health check

`GET /health` returns `200 OK`; Docker's built-in `HEALTHCHECK` polls it. Check
status with `docker ps` or `docker inspect`.

## Troubleshooting

| Symptom | Cause & fix |
|---|---|
| Port 80 already in use | Run with `-p 8080:80`; find the conflict with `sudo lsof -i :80` |
| Container exits / MySQL won't start | A corrupt `aureus-mysql` volume — recreate it: `docker volume rm aureus-mysql` |
| `404` on a `.js`/asset that should work, "from disk cache" | A stale browser cache — hard-reload (Ctrl/Cmd+Shift+R) or use a private window |
| HTTPS redirect on local HTTP | Run with `-e APP_ENV=local` — see [HTTP vs HTTPS](#http-vs-https) |
| External DB connection fails | Verify the server is reachable and the database/user exist; for a DB on the host use `host.docker.internal` |
| Services not running | `docker exec aureuserp supervisorctl status`; restart with `supervisorctl restart <name>` |
| Queue worker restarts at cold start | Expected for a few seconds until MySQL is ready — Supervisor retries automatically |
| `mysqld` fails to initialise during build | Some hosts enforce AppArmor on `mysqld`; build on a host without that restriction |

## Notes & limitations

**Configurable at runtime** (environment variables): database connection,
`APP_ENV`, `APP_DEBUG`, app URL, name, locale, currency, timezone, encryption
key.

**Set at build time** (build arguments): AureusERP ref, repository, PHP version,
Node version, admin account. Service configs (`nginx.conf`, `php.ini`,
`php-fpm.conf`, `supervisord.conf`) are baked — mount a replacement file over the
target path to change one.

- All services log to stdout/stderr — view with `docker logs`.
- The image is roughly 1.3–2 GB (bundled MySQL, PHP extensions, the application,
  and dependencies).
- No built-in TLS — put a reverse proxy in front for HTTPS.
- Never expose MySQL port 3306 publicly.

## Support

- Issues: <https://github.com/aureuserp/aureuserp/issues>
- Forum: <https://forums.aureuserp.com>
- Docs: <https://devdocs.aureuserp.com>
- Source: <https://github.com/aureuserp/aureuserp>
