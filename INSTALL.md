Netgen Content Browser installation instructions
================================================

Use Composer
------------

Run the following command to install Netgen Content Browser:

```
composer require netgen/content-browser-bundle:^1.0
```

Activate the bundle
-------------------

Activate the Content Browser in your kernel class:

```
...

$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();

return $bundles;
```

Activate the routes
-------------------

Add the following to your main `routing.yml` file to activate Content Browser routes:

```
netgen_content_browser:
    resource: "@NetgenContentBrowserBundle/Resources/config/routing.yml"
    prefix: "%netgen_content_browser.route_prefix%"
```
