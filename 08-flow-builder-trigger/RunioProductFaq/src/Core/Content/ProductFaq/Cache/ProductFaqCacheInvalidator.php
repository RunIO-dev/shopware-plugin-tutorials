<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\Cache;

use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Pages that show questions are tagged by the route; this clears them when a question changes.
 */
class ProductFaqCacheInvalidator implements EventSubscriberInterface
{
    public const TAG = 'runio-product-faq';

    public function __construct(private readonly CacheInvalidator $cacheInvalidator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'runio_product_faq.written' => 'onWritten',
            'runio_product_faq.deleted' => 'invalidate',
        ];
    }

    public function onWritten(EntityWrittenEvent $event): void
    {
        foreach ($event->getWriteResults() as $result) {
            // A new, inactive question (asked by a customer) changes no page
            $newAndInactive = $result->getOperation() === EntityWriteResult::OPERATION_INSERT
                && ($result->getPayload()['active'] ?? null) === false;

            if (!$newAndInactive) {
                $this->invalidate();

                return;
            }
        }
    }

    public function invalidate(): void
    {
        $this->cacheInvalidator->invalidate([self::TAG]);
    }
}
