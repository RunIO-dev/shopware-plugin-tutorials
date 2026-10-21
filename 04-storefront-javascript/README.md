# 04 · A storefront JavaScript plugin

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 3](../03-admin-module/), version 1.2.1. It adds a search box above the product questions. As you type, questions that don't match are hidden and matching ones open, so the answer is visible right away. A visible label, and a status line that screen readers announce ("2 of 5 questions match"), keep it usable for everyone.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq
shopware-cli project admin-build                        # the admin module from episode 3
bin/console theme:compile                               # picks up the built storefront JavaScript
bin/console cache:clear
```

The built storefront JavaScript is included in `src/Resources/app/storefront/dist/`, so you only need `shopware-cli project storefront-build` after changing the source.

## The storefront files

| File | What it does |
|---|---|
| `src/Resources/app/storefront/src/main.js` | Registers the plugin with `window.PluginManager`: a name, a dynamic `import()` of the class, and the selector `[data-runio-faq-search]`. Because of the dynamic import, the file is only downloaded on pages that contain the FAQ |
| `src/Resources/app/storefront/src/runio-faq-search/runio-faq-search.plugin.js` | The plugin class. It extends `PluginBaseClass`, keeps its selectors and the status text in `static options`, finds its elements through `this.el` in `init()`, filters on every keystroke, restores the questions the customer had open when the box is cleared, updates the `role="status"` line, and publishes `runioFaqSearch/filtered` on `this.$emitter` so other plugins can react |
| `src/Resources/views/storefront/component/product/description.html.twig` | Adds the `data-runio-faq-search` attribute with the translated status text as options, a labelled search input, the "no results" message and a visually hidden `role="status"` line |
| `src/Resources/app/storefront/dist/storefront/js/runio-product-faq/` | The built files that ship with the plugin |

## Good to know

- Options can be changed from a template without touching the JavaScript. The template already passes the translated status text this way: `data-runio-faq-search-options="{{ { statusText: 'runioProductFaq.status'|trans|striptags }|json_encode }}"`.
- Other plugins can listen to the search: `PluginManager.getPluginInstanceFromElement(el, 'RunioFaqSearch').$emitter.subscribe('runioFaqSearch/filtered', (event) => { … })`.
