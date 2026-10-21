# 09 · A scheduled task: clean up unanswered questions

**Video:** coming soon on [youtube.com/@amer](https://www.youtube.com/@amer)

This is `RunioProductFaq` from [episode 8](../08-flow-builder-trigger/), version 1.7.0. Once a day, a scheduled task deletes questions that nobody answered: inactive, without an answer, and older than a number of days the shop owner sets (default 90, `0` keeps them forever).

## Install

```bash
cp -r RunioProductFaq <your-shop>/custom/plugins/
cd <your-shop>
bin/console plugin:refresh
bin/console plugin:install --activate RunioProductFaq   # or plugin:update RunioProductFaq
bin/console cache:clear
```

Installing or updating registers the task. A worker has to be running for scheduled tasks to execute: `bin/console scheduled-task:run` together with `bin/console messenger:consume`, or the admin worker in development.

## Try it

```bash
bin/console scheduled-task:list | grep runio
# | runio_product_faq.delete_unanswered | <next execution> | ... | 86400 | scheduled |

# Run it right now instead of waiting for the next day
bin/console scheduled-task:run-single runio_product_faq.delete_unanswered
```

Change the number of days in the admin under **Extensions → My extensions → Product FAQ → Configure**, or with `bin/console system:config:set RunioProductFaq.config.deleteUnansweredAfterDays 30`.

## The new files

| File | What it does |
|---|---|
| `src/Core/Content/ProductFaq/ScheduledTask/DeleteUnansweredQuestionsTask.php` | The task: a name, a daily interval, and "try again next time" on failure |
| `src/Core/Content/ProductFaq/ScheduledTask/DeleteUnansweredQuestionsTaskHandler.php` | The work: reads the setting, finds inactive questions without an answer that are older than that, and deletes them in batches of 500 |
| `src/Resources/config/config.xml` | The setting `deleteUnansweredAfterDays` on the plugin's configuration page, default 90 |

Also changed: `services.php` tags the task with `shopware.scheduled.task` and the handler with `messenger.message_handler`.

## Good to know

- Only inactive questions without an answer are deleted. An inactive question that already has an answer is treated as a draft and kept.
- The handler uses `$this->now()` from Shopware's clock, in UTC like `createdAt`, so tests can set any date.
- If a batch comes back unchanged, the loop stops instead of running forever.
- The setting is read globally. The admin shows a sales channel switch for every plugin configuration, but a per-channel value has no effect here, because questions belong to products, not sales channels.
