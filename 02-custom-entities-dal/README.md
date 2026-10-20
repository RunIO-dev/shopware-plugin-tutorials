# 02 · Custom entities and the DAL

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

`RunioProductFaq` adds questions and answers to products. They are stored in a custom entity, `runio_product_faq`, and shown under the product description in the storefront. Shopware's Data Abstraction Layer (DAL) handles reading, writing, validation and the Admin API.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # runs the migration, creates the table
bin/console theme:compile
bin/console cache:clear
```

## Add questions through the Admin API

The DAL creates Admin API routes for the entity, so no controller is needed. Get a token first (the user needs Admin API access):

```bash
curl -X POST https://your-shop/api/oauth/token \
  -H 'Content-Type: application/json' \
  -d '{"grant_type":"password","client_id":"administration","username":"<user>","password":"<password>","scopes":"write"}'
```

Then create a question for a product:

```bash
curl -X POST https://your-shop/api/runio-product-faq \
  -H 'Authorization: Bearer <token>' -H 'Content-Type: application/json' \
  -d '{"productId":"<product id>","question":"Is it dishwasher safe?","answer":"Yes.","position":1}'
```

Search with a filter and sorting:

```bash
curl -X POST https://your-shop/api/search/runio-product-faq \
  -H 'Authorization: Bearer <token>' -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"filter":[{"type":"equals","field":"active","value":true}],"sort":[{"field":"position"}]}'
```

## The files

| File | What it does |
|---|---|
| `src/Migration/Migration1792533109CreateProductFaqTable.php` | Creates the table. Products are versioned, so the link to a product uses two columns: `product_id` and `product_version_id` |
| `src/Core/Content/ProductFaq/ProductFaqDefinition.php` | Describes the entity: its name (same as the table) and its fields. `Required` flags make the DAL reject incomplete writes |
| `src/Core/Content/ProductFaq/ProductFaqEntity.php` | Typed PHP object for one question |
| `src/Core/Content/ProductFaq/ProductFaqCollection.php` | Typed collection of questions |
| `src/Resources/config/services.php` | Registers the definition with the `shopware.entity.definition` tag, and the subscriber with the generated `runio_product_faq.repository` |
| `src/Subscriber/ProductFaqSubscriber.php` | On the product page, searches the active questions of the product (variants use their parent's) sorted by position, and adds them to the page |
| `src/Resources/views/storefront/component/product/description.html.twig` | Extends the description block and lists the questions after it |
| `src/Resources/snippet/runio-product-faq.*.json` | The heading text in English and German, loaded automatically by Shopware |
| `src/RunioProductFaq.php` | On uninstall, drops the table unless the shop owner chose to keep the plugin's data |

## Good to know

- Table and entity names carry a vendor prefix (`runio_`), so they never clash with Shopware or other plugins.
- Questions belong to the main product. Variants show their parent's questions, so a variant cannot have its own.
- Answers are plain text: without the `AllowHtml` flag, the DAL strips HTML on write.
- Next episode: an admin module, so the shop owner can manage the questions without API requests.
