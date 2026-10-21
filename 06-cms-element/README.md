# 06 · A CMS element for Shopping Experiences

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 5](../05-store-api-route/), version 1.4.0. It adds a **Product FAQ** CMS element, so shop owners can place a product's questions on any layout in *Content → Shopping Experiences*: a care guide, a landing page, a category page or a product page layout.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq
shopware-cli project admin-build                        # the admin module and the CMS element
bin/console theme:compile                               # the storefront JavaScript from episode 4
bin/console cache:clear
```

## Use it

1. Open a layout in *Content → Shopping Experiences*, and add any block, for example *Text*.
2. On the block's slot, click the swap icon and choose **Product FAQ**.
3. Open the element settings and choose a product. On a **product page layout**, leave it empty: the element then shows the questions of the page's product.

The search from episode 4 works inside the element too, because it binds to the `data-runio-faq-search` attribute.

## The new files

| File | What it does |
|---|---|
| `src/Resources/app/administration/src/module/sw-cms/elements/runio-product-faq/index.js` | Registers the element with `registerCmsElement`: name, label, the three components, and the default config (one `product` setting) |
| `…/runio-product-faq/component/index.js` | How the element looks in the layout editor. It loads the chosen product's questions with the admin repository |
| `…/runio-product-faq/config/index.js` | The settings dialog: a product picker bound to `element.config.product.value` |
| `…/runio-product-faq/preview/index.js` | The tile in the "choose an element" dialog |
| `src/Core/Content/ProductFaq/Cms/ProductFaqCmsElementResolver.php` | Fills the element with data on the storefront. Its `getType()` matches the element name. It calls the Store API route from episode 5, so visibility and filters stay in one place |
| `src/Resources/views/storefront/element/cms-element-runio-product-faq.html.twig` | The storefront template. Shopware finds it by its name: `cms-element-` + the element name |
| `src/Resources/views/storefront/component/runio-faq/faq-list.html.twig` | The FAQ markup, now shared by the product description and the CMS element. The search input's id comes from the caller, so two lists on one page don't clash |
| `src/Core/Content/ProductFaq/Cache/ProductFaqCacheInvalidator.php` | Clears cached pages that show questions when a question is written or deleted. The route tags those pages with `runio-product-faq` |

## Good to know

- The element renders nothing on the storefront when no product resolves (no product chosen on a normal layout, or the product was deleted or hidden). The editor shows a hint instead.
- In the editor, the preview filters by the chosen product id. If you pick a variant, the editor shows no questions, but the storefront shows the parent product's questions, like the product page does.
- Cache invalidation is new in this version and fixes stale questions on cached pages since episode 2. It was not shown in the video.
