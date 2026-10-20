<?php declare(strict_types=1);

namespace Demo\TopBar\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TopBarSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly SystemConfigService $config)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [StorefrontRenderEvent::class => 'onRender'];
    }

    public function onRender(StorefrontRenderEvent $event): void
    {
        // since 6.7 the header is rendered in its own request (ESI)
        if ($event->getRequest()->attributes->get('_route') !== 'frontend.header') {
            return;
        }

        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        $text = $this->config->getString('DemoTopBar.config.text', $salesChannelId);

        $event->setParameter('topBarText', $text);
    }
}
