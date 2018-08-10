Netgen Content Browser installation instructions
================================================

Use Composer
------------

Run the following command to install Netgen Content Browser:

```
composer require netgen/content-browser
```

Activate the bundles
--------------------

Activate the Content Browser in your kernel class with all required bundles:

```
...

$bundles[] = new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle();
$bundles[] = new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle();

return $bundles;
```

Activate the routes
-------------------

Add the following to your main `routing.yml` file to activate Content Browser
routes:

```
netgen_content_browser:
    resource: "@NetgenContentBrowserBundle/Resources/config/routing.yml"
    prefix: "%netgen_content_browser.route_prefix%"
```

Install assets
--------------

Run the following from your repo root to install Content Browser assets:

```
php app/console assets:install --symlink --relative
```
