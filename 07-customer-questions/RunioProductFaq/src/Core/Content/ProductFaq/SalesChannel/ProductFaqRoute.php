<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel;

use Runio\ProductFaq\Core\Content\ProductFaq\Cache\ProductFaqCacheInvalidator;
use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqCollection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductException;
use Shopware\Core\Framework\Adapter\Cache\CacheTagCollector;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\StoreApiRouteScope;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StoreApiRouteScope::ID]])]
class ProductFaqRoute extends AbstractProductFaqRoute
{
    /**
     * @param SalesChannelRepository<ProductCollection> $productRepository
     * @param EntityRepository<ProductFaqCollection> $faqRepository
     */
    public function __construct(
        private readonly SalesChannelRepository $productRepository,
        private readonly EntityRepository $faqRepository,
        private readonly CacheTagCollector $cacheTagCollector,
    ) {
    }

    public function getDecorated(): AbstractProductFaqRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(
        path: '/store-api/runio/product/{productId}/faq',
        name: 'store-api.runio.product-faq',
        methods: ['GET', 'POST'],
        defaults: ['_entity' => 'runio_product_faq'],
    )]
    public function load(
        string $productId,
        Criteria $criteria,
        SalesChannelContext $context,
    ): ProductFaqRouteResponse {
        // The sales channel repository only finds products this sales channel shows
        $productCriteria = (new Criteria([$productId]))->addFields(['parentId']);
        $product = $this->productRepository->search($productCriteria, $context)->getEntities()->first();

        if ($product === null) {
            throw ProductException::productNotFound($productId);
        }

        // Variants share the questions of their parent product
        $criteria
            ->addFilter(new EqualsFilter('productId', $product->get('parentId') ?? $productId))
            ->addFilter(new EqualsFilter('active', true))
            ->addFilter(new NotFilter(NotFilter::CONNECTION_OR, [
                new EqualsFilter('answer', null),
                new EqualsFilter('answer', ''),
            ]))
            ->addSorting(new FieldSorting('position'));

        // Cached pages that show questions are cleared when a question changes
        $this->cacheTagCollector->addTag(ProductFaqCacheInvalidator::TAG);

        return new ProductFaqRouteResponse($this->faqRepository->search($criteria, $context->getContext()));
    }
}
