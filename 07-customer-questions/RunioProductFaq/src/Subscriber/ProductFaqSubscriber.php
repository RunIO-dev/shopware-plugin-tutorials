<?php declare(strict_types=1);

namespace Runio\ProductFaq\Subscriber;

use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\AbstractProductFaqRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductFaqSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly AbstractProductFaqRoute $faqRoute)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ProductPageLoadedEvent::class => 'onProductPage'];
    }

    public function onProductPage(ProductPageLoadedEvent $event): void
    {
        // The storefront uses the same route as apps and headless frontends
        $faqs = $this->faqRoute
            ->load($event->getPage()->getProduct()->getId(), new Criteria(), $event->getSalesChannelContext())
            ->getFaqs();

        $event->getPage()->addExtension('runioFaqs', $faqs);
    }
}
