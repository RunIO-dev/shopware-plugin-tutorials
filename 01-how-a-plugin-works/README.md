# 01 · How a plugin works

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

`DemoTopBar` shows an announcement bar above the storefront header. The shop owner sets its text in the admin, and no file of the Shopware core is touched.

## Install

```bash
cp -r DemoTopBar <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate DemoTopBar
bin/console theme:compile
bin/console cache:clear
```

Then set the text in the admin under **Extensions → My extensions → Top bar → Configure**.

## The files

| File | What it does |
|---|---|
| `composer.json` | The plugin's ID card: name, version, the Shopware versions it needs, and where its main class is |
| `src/DemoTopBar.php` | The main class. It extends Shopware's `Plugin` class and is the plugin's entry point. Lifecycle methods (`install`, `activate`, `uninstall`, …) go here when needed |
| `src/Subscriber/TopBarSubscriber.php` | Listens to `StorefrontRenderEvent`, reads the bar text from the settings and passes it to the template |
| `src/Resources/config/services.php` | Registers the subscriber. Without the `kernel.event_subscriber` tag it is never called, and you get no error |
| `src/Resources/views/storefront/layout/header.html.twig` | Extends the core header template: opens the `header` block, adds the bar, then calls `parent()` so the original header stays |
| `src/Resources/config/config.xml` | Defines the text field. Shopware builds the settings form in the admin from it |
| `src/Resources/app/storefront/src/scss/base.scss` | The bar's styles, using the theme's primary colour |

## Good to know

Since Shopware 6.7 the header is rendered in its own request (ESI). `StorefrontRenderEvent` fires for the page and for the header separately, so the subscriber checks for the `frontend.header` route and only adds the text there.
