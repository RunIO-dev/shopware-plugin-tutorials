<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

/**
 * Once a day, delete questions that nobody answered within the configured number of days
 */
class DeleteUnansweredQuestionsTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'runio_product_faq.delete_unanswered';
    }

    public static function getDefaultInterval(): int
    {
        return self::DAILY;
    }

    // If a run fails, try again at the next interval instead of stopping the task
    public static function shouldRescheduleOnFailure(): bool
    {
        return true;
    }
}
