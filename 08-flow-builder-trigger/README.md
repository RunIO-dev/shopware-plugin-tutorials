# 08 · A Flow Builder trigger: email the shop owner about new questions

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 7](../07-customer-questions/), version 1.6.0. When a customer asks a question, the plugin fires its own Flow Builder trigger, **Runio / Product FAQ / Question asked**. The plugin has no mail code at all. The shop owner builds a flow in the admin, for example **Send email** to the admin with the ready-made template.

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq (runs the new migration)
shopware-cli project admin-build
bin/console cache:clear
```

## Try it

1. In the admin, open **Automation → Flow Builder → Add flow**.
2. Under **Flow**, choose the trigger **Runio / Product FAQ / Question asked**. Typing "question" finds it.
3. Add the action **Send email**, pick the template **Product question asked** and choose **Admin** as the recipient. Save and activate the flow.
4. Log in to the storefront as a customer and ask a question on a product page. The shop owner gets an email with the customer, the product and the question.

## The new files

| File | What it does |
|---|---|
| `src/Core/Content/ProductFaq/Event/ProductFaqAskedEvent.php` | The trigger. `FlowEventAware` plus `SalesChannelAware`, `CustomerAware`, `ProductAware`, `MailAware` and `ScalarValuesAware` tell the Flow Builder what data it carries. `getAvailableData()` describes it for mail templates |
| `src/Subscriber/BusinessEventCollectorSubscriber.php` | Adds the event to the `BusinessEventCollector`, so it appears in the list of triggers |
| `src/Migration/Migration1792572680AddQuestionAskedMailTemplate.php` | A mail template type `runio_product_faq.asked` and a ready-made template in English and German, written with Shopware's `ImportTranslationsTrait` |

Also changed:
- The ask route dispatches the event right after it saves the question.
- Admin snippets name the trigger: `sw-flow.triggers.*` for the trigger tree, `global.businessEvents.runio_product_faq_asked` for the list of flows.
- Uninstalling without keeping data also removes the flows that use the trigger, the template and its type.

## Good to know

- Event names become snippet keys: `runio.product_faq.asked` is shown as `sw-flow.triggers.runio`, `.productFaq` and `.asked` in the tree, and as `global.businessEvents.runio_product_faq_asked` in the list.
- Flows store ids, not entities. The Flow Builder reloads the customer and the product itself, so the getters only return ids and the question.
- If a flow fails, the question is still saved. Shopware logs the error, and the customer still gets the thank-you message.
- Twig escapes `{{ question }}` in the HTML mail. The route also strips tags before saving.
