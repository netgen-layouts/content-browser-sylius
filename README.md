Netgen Content Browser
======================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

You can run unit tests by simply calling Composer `test` from the repo root:

```
$ composer test
```

# Running API tests

API tests are functional tests, meaning they need a fully functional Symfony app
with Content Browser enabled.

To run the tests, you need to require some Composer packages:

```
composer require lakion/api-test-case
```

Simplest way for tests to authenticate to your app is to enable basic auth in your `security.yml`:

```
security:
    firewalls:
        main:
            http_basic: ~
```

Afterwards, running tests is as simple as calling the following command:

```
SF_USERNAME=user SF_PASSWORD=password vendor/bin/phpunit --bootstrap vendor/autoload.php -c vendor/netgen/content-browser/phpunit-api.xml
```

Notice that you need to specify username and password for your Symfony app.
