# Symfony API

## Setup

Clone the project
```shell
$ git clone https://gitlab.fondative.com/stacks-fdt/applications/symfony-stacks/api.git
```

## Configure the App

Create `.env.local`.
Run `echo "USER_ID=$(id -u)" > .env.local` (only on linux).

## Setup data

```shell
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate -n
$ php bin/console fixture:load
```

## Start the App

```shell
$ symfony server:start -d
```