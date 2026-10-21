# 07 · Customers ask their own questions

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 6](../06-cms-element/), version 1.5.0. Logged-in customers can ask a question right on the product page. It is saved inactive and without an answer. The shop owner answers it in the admin and activates it, and then everyone sees it.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq (runs the new migration)
shopware-cli project admin-build
bin/console theme:compile
bin/console cache:clear
```

## Try it

In the storefront, log in, open a product with questions and use **Didn't find your answer? Ask us.** below the list. Or through the Store API, with a logged-in customer's context token:

```bash
curl -X POST https://<your-shop>/store-api/runio/product/<productId>/faq/ask \
  -H "sw-access-key: <access key>" -H "sw-context-token: <token>" \
  -H "Content-Type: application/json" \
  -d '{ "question": "Is the handle heat resistant?" }'
# 204 No Content: saved, inactive, waiting for an answer
```

| Response | When |
|---|---|
| `204` | The question was saved |
| `400` | The question is shorter than 10 or longer than 255 characters (after tags and outer spaces are removed) |
| `403` | Nobody is logged in |
| `404` | The product doesn't exist or isn't visible in the sales channel |
| `429` | The customer asked more than 5 questions in the last hour |

## The new files

| File | What it does |
|---|---|
| `src/Migration/Migration1792571341AllowQuestionsWithoutAnswer.php` | Makes `answer` nullable. A **new** migration, because shops that already ran the first one never run it again |
| `src/Core/Content/ProductFaq/SalesChannel/AskQuestionRoute.php` (+ abstract) | `POST /store-api/runio/product/{productId}/faq/ask`, login required, rate limited, validated with the `DataValidator`. Creates the question on the parent product, inactive. The client only sends the text |
| `src/Storefront/Controller/ProductFaqController.php` | Storefront wrapper: calls the route, shows a flash message, redirects back to the product |
| `src/Resources/views/storefront/component/runio-faq/ask-form.html.twig` | The form for logged-in customers, or a login link that comes back to the product |
| `src/Resources/config/rate_limiter.yaml` + `src/DependencyInjection/RateLimiterCompilerPass.php` | A `fixed_window` limit of 5 questions per customer and hour, added to Shopware's rate limiter configuration from the plugin's `build()` method |

Also changed: `answer` is no longer `Required` in the definition and the admin form, the list route skips questions without an answer (even if active), and new inactive questions don't clear the page cache.

## Good to know

- New questions get position 0, so once answered they appear first. Set a position in the admin if you want them elsewhere.
- Rate limits need a persistent cache. With an in-memory (`array`) app cache, as in some dev setups, the limiter forgets between requests.
- Shopware 6.7 has no CSRF tokens: storefront POSTs are protected by the `SameSite=Lax` session cookie, like core's own forms.
