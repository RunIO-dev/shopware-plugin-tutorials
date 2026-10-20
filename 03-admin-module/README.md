# 03 · An admin module for your entity

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 2](../02-custom-entities-dal/), version 1.1.0. It adds an admin module: a list of all product questions and a form to create and edit them, under **Catalogues → Product FAQ**. The shop owner no longer needs API requests.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq if episode 2 is installed
shopware-cli project admin-build                        # builds the admin JavaScript
bin/console theme:compile
bin/console cache:clear
```

The built admin files (`src/Resources/public/`) are not in this repository, so the admin build step is required.

## The admin files

All admin code lives in `src/Resources/app/administration/src/`.

| File | What it does |
|---|---|
| `main.js` | Entry point of the plugin's admin code. It only imports the module |
| `module/runio-product-faq/index.js` | `Shopware.Module.register()`: name, color, icon, entity, the routes (list, create, detail) and the menu entry under Catalogues |
| `module/runio-product-faq/page/runio-product-faq-list/` | The list. A repository from `repositoryFactory`, a `Criteria` that loads the product with each question and sorts by position, and `sw-entity-listing` for the table |
| `module/runio-product-faq/page/runio-product-faq-detail/` | The form. Meteor components (`sw-entity-single-select`, `mt-text-field`, `mt-textarea`, `mt-number-field`, `mt-switch`), `repository.create()` / `get()` / `save()`, and `mapPropertyErrors` to show the DAL's `Required` errors under each field |
| `module/runio-product-faq/acl/index.js` | Permissions: viewer, editor, creator and deleter roles, shown in **Settings → Users & permissions** |
| `module/runio-product-faq/snippet/*.json` | English and German texts. Shopware loads them automatically from the `snippet` folder |

## Good to know

- Route names come from the module name: `runio-product-faq` becomes `runio.product.faq.list`, `.create` and `.detail`.
- Administrators see the module right away. Other users need a role with the Product FAQ permissions.
