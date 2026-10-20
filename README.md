# Shopware Plugin Tutorials

The code from the **Shopware Plugin Development** video series by [Runio](https://runio.dev) on YouTube ([@amer](https://www.youtube.com/@amer)).

Each folder is one episode and holds the exact plugin built in that video. Every plugin works on its own: copy its folder into `custom/plugins/` of a Shopware 6.7 shop.

| # | Episode | Plugin | What you learn |
|---|---|---|---|
| 01 | [How a plugin works](01-how-a-plugin-works/) | `DemoTopBar` | Plugin skeleton, `composer.json`, lifecycle, event subscribers, extending Twig templates, plugin settings |

More episodes are on the way: custom entities and the DAL, an admin module, storefront JavaScript plugins, Store API routes, CMS elements, Flow Builder actions, and scheduled tasks.

## Requirements

- Shopware 6.7
- A local shop to try things in. The videos use one created with [shopware-cli](https://github.com/shopware/shopware-cli) running in Docker.

## Checks

Every push runs Shopware's own extension checker (`shopware-cli extension validate`: PHPStan, Twig, SCSS and metadata checks) on each plugin.

## License

The code is MIT-licensed, see [LICENSE](LICENSE). You can use it in your own projects.

The videos are not covered by this license: © 2026 Runio, all rights reserved. Please link to them instead of re-uploading them.

Not affiliated with or endorsed by shopware AG. Shopware is a trademark of shopware AG.
