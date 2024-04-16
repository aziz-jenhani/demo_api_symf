# Symfony API

## Setup

Clone the project
```shell
git clone https://github.com/aziz-jenhani/demo_api_symf.git
```
Composer Installation
```shell
composer install
```
## Configure the App

Create `.env.local`.
Run `echo "USER_ID=$(id -u)" > .env.local` (only on linux).

## Setup data

```shell
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n
php bin/console fixture:load
```

## Start the App

```shell
 symfony server:start -d
```
## Navigate to Backend documentations
```url
 http://127.0.0.1:8001/api/doc
```
