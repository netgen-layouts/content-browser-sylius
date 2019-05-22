# Netgen Content Browser & Sylius integration installation instructions

## Use Composer

Run the following command to install Netgen Content Browser & Sylius integration:

```
composer require netgen/content-browser-sylius
```

## Activate the bundles

Activate the Content Browser in your kernel class with all required bundles:

```
...

$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserSyliusBundle\NetgenContentBrowserSyliusBundle();

return $bundles;
```
