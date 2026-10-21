<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel;

use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\NoContentResponse;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

abstract class AbstractAskQuestionRoute
{
    abstract public function getDecorated(): AbstractAskQuestionRoute;

    abstract public function ask(
        string $productId,
        RequestDataBag $data,
        SalesChannelContext $context,
    ): NoContentResponse;
}
