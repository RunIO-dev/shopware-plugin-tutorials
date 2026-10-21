<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractProductFaqRoute
{
    abstract public function getDecorated(): AbstractProductFaqRoute;

    abstract public function load(
        string $productId,
        Criteria $criteria,
        SalesChannelContext $context,
    ): ProductFaqRouteResponse;
}
