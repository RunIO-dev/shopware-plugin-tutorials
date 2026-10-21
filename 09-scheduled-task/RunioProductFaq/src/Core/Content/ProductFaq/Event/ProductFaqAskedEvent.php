<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\Event;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Flow\Dispatching\Aware\ScalarValuesAware;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\CustomerAware;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\EventData\ScalarValueType;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\ProductAware;
use Shopware\Core\Framework\Event\SalesChannelAware;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * A customer asked a question. Each Aware interface tells the Flow Builder what data it carries.
 */
final class ProductFaqAskedEvent extends Event implements FlowEventAware, SalesChannelAware, CustomerAware, ProductAware, MailAware, ScalarValuesAware
{
    public const EVENT_NAME = 'runio.product_faq.asked';

    public function __construct(
        private readonly SalesChannelContext $salesChannelContext,
        private readonly CustomerEntity $customer,
        private readonly string $productId,
        private readonly string $question,
    ) {
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add(CustomerAware::CUSTOMER, new EntityType(CustomerDefinition::class))
            ->add(ProductAware::PRODUCT, new EntityType(ProductDefinition::class))
            ->add('question', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelContext->getSalesChannelId();
    }

    public function getCustomerId(): string
    {
        return $this->customer->getId();
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getValues(): array
    {
        return ['question' => $this->question];
    }

    // The default recipient is the customer. In the flow, the shop owner can send to the admin instead.
    public function getMailStruct(): MailRecipientStruct
    {
        return new MailRecipientStruct([
            $this->customer->getEmail() => trim($this->customer->getFirstName() . ' ' . $this->customer->getLastName()),
        ]);
    }
}
