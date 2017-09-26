Netgen Content Browser
======================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

You can run unit tests by simply calling `phpunit` from the repo root.

```
$ vendor/bin/phpunit
```

# Running API tests

API tests are functional tests, meaning they need a fully functional Symfony app
with Content Browser enabled.

To run the tests, you need to require some Composer packages:

```
composer require lakion/api-test-case:^1.0|^2.0
```

Afterwards, running tests is as simple as calling the following command:

```
EZ_USERNAME=admin EZ_PASSWORD=publish vendor/bin/phpunit --bootstrap app/autoload.php -c vendor/netgen/content-browser/phpunit-api.xml
```
