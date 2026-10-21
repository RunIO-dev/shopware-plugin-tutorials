<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\Cms;

use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\AbstractProductFaqRoute;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class ProductFaqCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(private readonly AbstractProductFaqRoute $faqRoute)
    {
    }

    public function getType(): string
    {
        return 'runio-product-faq';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        // Nothing to batch: enrich() asks our Store API route
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $productId = $slot->getFieldConfig()->get('product')?->getStringValue();

        // On a product page layout, no product in the settings means "the product of this page"
        if (!$productId
            && $resolverContext instanceof EntityResolverContext
            && $resolverContext->getDefinition()->getEntityName() === 'product') {
            $productId = $resolverContext->getEntity()->getUniqueIdentifier();
        }

        if (!$productId) {
            return;
        }

        try {
            $route = $this->faqRoute->load($productId, new Criteria(), $resolverContext->getSalesChannelContext());
        } catch (ProductNotFoundException) {
            // A product that was deleted or hidden after the layout was made
            return;
        }

        $slot->setData($route->getFaqs());
    }
}
