<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ProductFaqEntity>
 */
class ProductFaqCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductFaqEntity::class;
    }
}
