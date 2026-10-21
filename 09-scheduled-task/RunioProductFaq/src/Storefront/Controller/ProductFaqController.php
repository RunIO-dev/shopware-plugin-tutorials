<?php declare(strict_types=1);

namespace Runio\ProductFaq\Storefront\Controller;

use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\AbstractAskQuestionRoute;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\Framework\RateLimiter\Exception\RateLimitExceededException;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Routing\StorefrontRouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StorefrontRouteScope::ID]])]
class ProductFaqController extends StorefrontController
{
    public function __construct(private readonly AbstractAskQuestionRoute $askQuestionRoute)
    {
    }

    #[Route(
        path: '/runio/product/{productId}/faq/ask',
        name: 'frontend.runio.product-faq.ask',
        methods: ['POST'],
        defaults: [PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED => true],
    )]
    public function ask(string $productId, RequestDataBag $data, SalesChannelContext $context): Response
    {
        try {
            $this->askQuestionRoute->ask($productId, $data, $context);
            $this->addFlash(self::SUCCESS, $this->trans('runioProductFaq.askSuccess'));
        } catch (ConstraintViolationException) {
            $this->addFlash(self::DANGER, $this->trans('runioProductFaq.askInvalid'));
        } catch (RateLimitExceededException) {
            $this->addFlash(self::DANGER, $this->trans('runioProductFaq.askTooMany'));
        }

        return $this->redirectToRoute('frontend.detail.page', ['productId' => $productId]);
    }
}
