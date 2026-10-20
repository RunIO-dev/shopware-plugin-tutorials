<?php declare(strict_types=1);

namespace Runio\ProductFaq\Subscriber;

use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductFaqSubscriber implements EventSubscriberInterface
{
    /**
     * @param EntityRepository<ProductFaqCollection> $faqRepository
     */
    public function __construct(private readonly EntityRepository $faqRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ProductPageLoadedEvent::class => 'onProductPage'];
    }

    public function onProductPage(ProductPageLoadedEvent $event): void
    {
        $product = $event->getPage()->getProduct();

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('productId', $product->getParentId() ?? $product->getId()))
            ->addFilter(new EqualsFilter('active', true))
            ->addSorting(new FieldSorting('position'));

        $faqs = $this->faqRepository->search($criteria, $event->getContext())->getEntities();

        $event->getPage()->addExtension('runioFaqs', $faqs);
    }
}
