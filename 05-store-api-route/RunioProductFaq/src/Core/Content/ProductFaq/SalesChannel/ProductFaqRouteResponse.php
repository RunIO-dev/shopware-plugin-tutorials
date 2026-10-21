<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel;

use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * @extends StoreApiResponse<EntitySearchResult<ProductFaqCollection>>
 */
class ProductFaqRouteResponse extends StoreApiResponse
{
    public function getFaqs(): ProductFaqCollection
    {
        return $this->object->getEntities();
    }
}
