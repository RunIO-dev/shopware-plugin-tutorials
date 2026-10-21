# 05 · Your own Store API route

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 4](../04-storefront-javascript/), version 1.3.0. It adds a Store API route, so mobile apps and headless frontends can read a product's questions:

```
GET|POST /store-api/runio/product/{productId}/faq
```

The storefront product page now uses the same route, so every frontend gets the same answers.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq
shopware-cli project admin-build                        # the admin module from episode 3
bin/console theme:compile                               # the storefront JavaScript from episode 4
bin/console cache:clear
```

## Try it

Find your sales channel's access key in the admin (Sales Channels → your channel → API access), then:

```bash
curl https://<your-shop>/store-api/runio/product/<productId>/faq \
  -H "sw-access-key: <access key>"

# only questions and answers, at most 2
curl -X POST https://<your-shop>/store-api/runio/product/<productId>/faq \
  -H "sw-access-key: <access key>" -H "Content-Type: application/json" \
  -d '{ "includes": { "runio_product_faq": ["question", "answer"] }, "limit": 2 }'
```

The route is also listed in your shop's API reference at `/store-api/_info/stoplightio.html` (under "Product").

## The new files

| File | What it does |
|---|---|
| `src/Core/Content/ProductFaq/SalesChannel/AbstractProductFaqRoute.php` | The type other code depends on. Other plugins can decorate the route (wrap it and change its result) without replacing ours |
| `src/Core/Content/ProductFaq/SalesChannel/ProductFaqRoute.php` | The route. It has the `store-api` route scope. It looks the product up through `sales_channel.product.repository`, which only finds products the sales channel shows. It loads only `parentId`, so variants get their parent's questions. Then it adds its own filters (active, sorted by position) on top of the client's criteria |
| `src/Core/Content/ProductFaq/SalesChannel/ProductFaqRouteResponse.php` | A `StoreApiResponse` that hands back the questions |
| `src/Resources/config/routes.php` | Tells Shopware to read the route attributes from our `*Route.php` classes |
| `src/Resources/config/services.php` | Registers the route as a **public** service (Symfony calls it as a controller), and gives the subscriber the route instead of the repository |
| `src/Resources/Schema/StoreApi/runio-product-faq.json` | OpenAPI description, so the route shows up in the API reference |
| `src/Core/Content/ProductFaq/ProductFaqDefinition.php` | Now flags `id`, `productId`, `question`, `answer` and `position` as `ApiAware`. Without the flag, the Store API leaves a field out |

## Good to know

- `active`, the version field and the `product` association are deliberately **not** `ApiAware`. Clients can't read them, and can't filter, sort or aggregate on them either.
- Client criteria are combined with ours, so a client can narrow the result down (limit, includes, extra filters on shared fields), but can't get inactive questions back.
- An unknown product, or one that isn't visible in the sales channel, returns `404 CONTENT__PRODUCT_NOT_FOUND`.
- There is no cache layer on this route on purpose: the product page is already HTTP-cached, and the route is one indexed query. If you add one, invalidate it when FAQs or products change.
