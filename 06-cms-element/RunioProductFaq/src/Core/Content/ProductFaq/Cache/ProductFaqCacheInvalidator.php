<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\Cache;

use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
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
            'runio_product_faq.written' => 'invalidate',
            'runio_product_faq.deleted' => 'invalidate',
        ];
    }

    public function invalidate(): void
    {
        $this->cacheInvalidator->invalidate([self::TAG]);
    }
}
