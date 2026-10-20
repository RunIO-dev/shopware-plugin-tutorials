<?php declare(strict_types=1);

namespace Runio\ProductFaq;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class RunioProductFaq extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->container->get(Connection::class)->executeStatement('DROP TABLE IF EXISTS `runio_product_faq`');
    }
}
