# Aurélien Lorence

# Formation Développeur d'application PHP / Symfony [OpenClassrooms]

## Projet n°5 - Créez votre premier blog en PHP

___

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/445f29eb459d4c7fbdecec870df1205e)](https://app.codacy.com/gh/AurelienLab/daps-p5-blog/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

# Project setup

## Requirements

This project requires `PHP 8.3` and the following php extensions:

- `curl`
- `json`
- `mbstring`

## Setup

### 1 - Import project sources

```shell
git clone git@github.com:AurelienLab/daps-p5-blog.git
cd daps-p5-blog
```

### 2 - Install dependencies

In projet root directory excute this command:

```
composer install
```

### 3 - Setup database

You'll need the empty database for this project. You can explicitely ask me for it in private message.
Once you imported it, remember the database name and your mysql credentials.

### 4 - Configure `.env`

```dotenv
APP_ENV=dev                             # Current environment (dev|prod)
APP_KEY=generateme                      # Random string used for encryption

APP_REMEMBER_ME_LIFETIME=336            # Number of hours a user will be remembered for auto login
APP_PASSWORD_REQUEST_HOURS_VALIDITY=24  # Number of hours of password request token validity
APP_CONTACT_EMAIL=contact@example.com   # Mail sender


################################
####### DATABASE CONFIG ########
################################
DB_HOST=localhost
DB_PORT=3306
DB_NAME=
DB_USER=
DB_PASSWORD=

# See https://symfony.com/doc/current/mailer.html for mailer configuration

MAILER_DSN=smtp://<user>:<password>@provider.com:2525
MAILER_DEFAULT_SENDER=example@example.net
```

### 5 - Configure web server

Your web server must target `public/` folder in project directory, and have FollowSymLinks enabled.
Follow [this documentation](https://nginx.org/en/docs/http/ngx_http_core_module.html#disable_symlinks) for nginx
or [this one](https://httpd.apache.org/docs/2.4/fr/mod/core.html#options) for apache2.

### 6 - Permissions

Your web server user must have rights to write in `public/uploads` folder and sub-folders. If you're running on Unix
system with ACL You can run the following:

```shell
# Get current web server user
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

# set permissions for future files and folders
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public/uploads
# set permissions on the existing files and folders
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public/uploads
```

Source: [https://symfony.com/doc/current/setup/file_permissions.html](https://symfony.com/doc/current/setup/file_permissions.html)
