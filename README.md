# ads-checker

Symfony 5 application that checks the presence of the ads.txt file on a given URL.

## Provisioning

To ease local development you have to install [Docker CE](https://www.docker.com/),
at least version 18.06.0.

**Note**: It is recommended to follow [this guide](https://docs.docker.com/install/linux/linux-postinstall/#manage-docker-as-a-non-root-user) in order to manage Docker as a non-root user.

First of all, the project image has to be built, executing from project's root folder the following command:

```
docker build -t ads-checker/php-composer:7.4 .
```

To install project dependencies, from project's root folder, run:

```
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer install
```

## Asynchronous tasks

### Execute the ads.txt checker command
To execute a PHP script, from project's root folder, run:

```
docker run --rm --interactive --tty --volume $PWD:/usr/src/myapp --user $(id -u):$(id -g) -w /usr/src/myapp ads-checker/php-composer:7.4 bin/console app:ads-checker https://www.ansa.it
```

### Execute CS-Fixer
To execute CS-Fixer, from project's root folder, run:

```
docker run --rm --interactive --tty --volume $PWD:/usr/src/myapp --user $(id -u):$(id -g) -w /usr/src/myapp ads-checker/php-composer:7.4 vendor/bin/php-cs-fixer fix --config .php_cs.dist --allow-risky yes
```

### Execute PHPUnit tests
To execute PHPUnit tests, from project's root folder, run:

```
docker run --rm --interactive --tty --volume $PWD:/usr/src/myapp --user $(id -u):$(id -g) -w /usr/src/myapp ads-checker/php-composer:7.4 bin/phpunit
```