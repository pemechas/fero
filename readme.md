# FERO PHP (Symfony) Coding Task

Processing simple cart checkouts.

## Prerequisites
You must have [php](https://www.php.net/manual/en/install.php) (version 8), [Mysql](https://dev.mysql.com/downloads/installer/) and [composer](https://getcomposer.org/download/) installed in your system.

## Installation

1. Copy env.example to a new .env file and uncomment the next line to configure mysql database conection in the project:

    * The first "app" is the name of the user, "!ChangeMe!" is the password for that user and the second "app" is the name of the schema.
    * If the username, password, host or database name contain any character considered special in a URI (such as : / ? # [ ] @ ! $ & ' ( ) * + , ; =), you must encode them. See [RFC 3986](https://www.ietf.org/rfc/rfc3986.txt) for the full list of reserved characters. You can use the [urlencode](https://www.php.net/manual/en/function.urlencode.php) function to encode them

```bash
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

    Example: DATABASE_URL="mysql://<username>:<password>@127.0.0.1:3306/<schema_name>?serverVersion=8.0.32&charset=utf8mb4"

2. Use the package manager [composer](https://getcomposer.org/download/) to install all the dependencies:

```bash
composer install
```

3. Run migrations for Database and tables creation:

```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

## Start server

1. Server will run under "http://127.0.0.1:8000" in dev environments
    * NOTE: In production environments, server should be something like [nginx](https://docs.nginx.com/nginx/admin-guide/installing-nginx/installing-nginx-open-source/). For simplicity I decided to use symfony built in server.

```bash
symfony serve
```

## Consume async messages

1. The webhook will be executed asynchronously using a queue so we do not potentially block the endpoint
2. To consume the messages run the next command:

```bash
php bin/console messenger:consume async -vv
```

## Logger

1. For simplicity, I am using the built in [logger](https://symfony.com/doc/current/components/console/logger.html) from symfony, instead of [Monolog](https://symfony.com/doc/current/logging.html#monolog)
2. In production, probably would be good to integrate some monitoring applications like [grafana](https://grafana.com/products/cloud/logs/) or [sentry](https://sentry.io/product/error-monitoring/)

## Tests

1. Run migrations to create test database

```bash
symfony console doctrine:database:create --env=test
symfony console doctrine:migrations:migrate -n --env=test
```

2. Run tests

```bash
php bin/phpunit
```

## License

[MIT](https://choosealicense.com/licenses/mit/)
