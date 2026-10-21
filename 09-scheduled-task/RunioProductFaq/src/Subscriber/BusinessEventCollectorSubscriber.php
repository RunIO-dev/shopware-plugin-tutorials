<?php declare(strict_types=1);

namespace Runio\ProductFaq\Subscriber;

use Runio\ProductFaq\Core\Content\ProductFaq\Event\ProductFaqAskedEvent;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds our event to the list of triggers in the Flow Builder
 */
class BusinessEventCollectorSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BusinessEventCollector $businessEventCollector)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // A high priority, so our trigger is there before others read the list
        return [BusinessEventCollectorEvent::NAME => ['onCollect', 1000]];
    }

    public function onCollect(BusinessEventCollectorEvent $event): void
    {
        $definition = $this->businessEventCollector->define(ProductFaqAskedEvent::class);

        if ($definition !== null) {
            $event->getCollection()->set($definition->getName(), $definition);
        }
    }
}
