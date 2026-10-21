# Shopware Plugin Tutorials

The code from the **Shopware Plugin Development** video series by [Runio](https://runio.dev) on YouTube ([@amer](https://www.youtube.com/@amer)).

Each folder is one episode and holds the exact plugin built in that video. Every plugin works on its own: copy its folder into `custom/plugins/` of a Shopware 6.7 shop.

| # | Episode | Plugin | What you learn |
|---|---|---|---|
| 01 | [How a plugin works](01-how-a-plugin-works/) | `DemoTopBar` | Plugin skeleton, `composer.json`, lifecycle, event subscribers, extending Twig templates, plugin settings |
| 02 | [Custom entities and the DAL](02-custom-entities-dal/) | `RunioProductFaq` | Migrations, entity definitions and flags, associations, repositories and Criteria, the generated Admin API, storefront snippets |
| 03 | [An admin module for your entity](03-admin-module/) | `RunioProductFaq` 1.1 | `Module.register`, routes and navigation, `sw-entity-listing`, Meteor form components, `mapPropertyErrors`, ACL roles, admin build |
| 04 | [A storefront JavaScript plugin](04-storefront-javascript/) | `RunioProductFaq` 1.2 | `PluginBaseClass`, `PluginManager.register`, lazy loading with `import()`, data attributes and options, `$emitter` events, storefront build |
| 05 | [Your own Store API route](05-store-api-route/) | `RunioProductFaq` 1.3 | Abstract route and decoration, `store-api` route scope, `routes.php`, sales channel repositories and visibility, `ApiAware`, criteria and `includes`, OpenAPI schema |
| 06 | [A CMS element for Shopping Experiences](06-cms-element/) | `RunioProductFaq` 1.4 | `registerCmsElement`, element component, config and preview, CMS data resolvers, product page layouts, storefront element templates, cache tags and invalidation |
| 07 | [Customers ask their own questions](07-customer-questions/) | `RunioProductFaq` 1.5 | A second migration, a POST Store API route with login, `DataValidator` and a rate limiter, a storefront controller with flash messages, login redirects |
| 08 | [A Flow Builder trigger](08-flow-builder-trigger/) | `RunioProductFaq` 1.6 | A `FlowEventAware` event with Aware interfaces, the `BusinessEventCollector`, a mail template type and template from a migration with `ImportTranslationsTrait`, uninstall cleanup |

More episodes are on the way: scheduled tasks.

## Requirements

- Shopware 6.7
- A local shop to try things in. The videos use one created with [shopware-cli](https://github.com/shopware/shopware-cli) running in Docker.

## Checks

Every push runs Shopware's own extension checker (`shopware-cli extension validate`: PHPStan, Twig, SCSS and metadata checks) on each plugin.

## License

The code is MIT-licensed, see [LICENSE](LICENSE). You can use it in your own projects.

The videos are not covered by this license: © 2026 Runio, all rights reserved. Please link to them instead of re-uploading them.

Not affiliated with or endorsed by shopware AG. Shopware is a trademark of shopware AG.
