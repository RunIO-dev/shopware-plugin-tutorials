<?php declare(strict_types=1);

use Runio\ProductFaq\Core\Content\ProductFaq\Cache\ProductFaqCacheInvalidator;
use Runio\ProductFaq\Core\Content\ProductFaq\Cms\ProductFaqCmsElementResolver;
use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqDefinition;
use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\AskQuestionRoute;
use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\ProductFaqRoute;
use Runio\ProductFaq\Storefront\Controller\ProductFaqController;
use Runio\ProductFaq\Subscriber\BusinessEventCollectorSubscriber;
use Runio\ProductFaq\Subscriber\ProductFaqSubscriber;
use Shopware\Core\Framework\Adapter\Cache\CacheInvalidator;
use Shopware\Core\Framework\Adapter\Cache\CacheTagCollector;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\RateLimiter\RateLimiter;
use Shopware\Core\Framework\Validation\DataValidator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(ProductFaqDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => 'runio_product_faq']);

    // Public, because Symfony calls the route as a controller
    $services->set(ProductFaqRoute::class)
        ->public()
        ->args([
            service('sales_channel.product.repository'),
            service('runio_product_faq.repository'),
            service(CacheTagCollector::class),
        ]);

    $services->set(AskQuestionRoute::class)
        ->public()
        ->args([
            service('sales_channel.product.repository'),
            service('runio_product_faq.repository'),
            service(DataValidator::class),
            service(RateLimiter::class),
            service('event_dispatcher'),
        ]);

    $services->set(ProductFaqController::class)
        ->public()
        ->args([service(AskQuestionRoute::class)])
        ->call('setContainer', [service('service_container')]);

    $services->set(ProductFaqCmsElementResolver::class)
        ->args([service(ProductFaqRoute::class)])
        ->tag('shopware.cms.data_resolver');

    $services->set(ProductFaqCacheInvalidator::class)
        ->args([service(CacheInvalidator::class)])
        ->tag('kernel.event_subscriber');

    $services->set(BusinessEventCollectorSubscriber::class)
        ->args([service(BusinessEventCollector::class)])
        ->tag('kernel.event_subscriber');

    $services->set(ProductFaqSubscriber::class)
        ->args([service(ProductFaqRoute::class)])
        ->tag('kernel.event_subscriber');
};
