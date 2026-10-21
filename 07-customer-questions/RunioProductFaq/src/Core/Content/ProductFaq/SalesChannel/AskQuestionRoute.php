<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel;

use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqCollection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\RateLimiter\RateLimiter;
use Shopware\Core\Framework\Routing\StoreApiRouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\NoContentResponse;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

#[Route(defaults: [PlatformRequest::ATTRIBUTE_ROUTE_SCOPE => [StoreApiRouteScope::ID]])]
class AskQuestionRoute extends AbstractAskQuestionRoute
{
    /**
     * @param SalesChannelRepository<ProductCollection> $productRepository
     * @param EntityRepository<ProductFaqCollection> $faqRepository
     */
    public function __construct(
        private readonly SalesChannelRepository $productRepository,
        private readonly EntityRepository $faqRepository,
        private readonly DataValidator $validator,
        private readonly RateLimiter $rateLimiter,
    ) {
    }

    public function getDecorated(): AbstractAskQuestionRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(
        path: '/store-api/runio/product/{productId}/faq/ask',
        name: 'store-api.runio.product-faq.ask',
        methods: ['POST'],
        defaults: [PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED => true],
    )]
    public function ask(
        string $productId,
        RequestDataBag $data,
        SalesChannelContext $context,
    ): NoContentResponse {
        $customer = $context->getCustomer();
        \assert($customer !== null);

        // At most 5 questions per customer and hour, then a 429
        $this->rateLimiter->ensureAccepted('runio_product_faq_ask', $customer->getId());

        // Validate what we store: the text without tags and outer spaces
        $question = $data->get('question');
        if (\is_string($question)) {
            $data->set('question', trim(strip_tags($question)));
        }

        $definition = (new DataValidationDefinition('runio_product_faq.ask'))
            ->add('question', new NotBlank(), new Type('string'), new Length(min: 10, max: 255));

        // Throws a ConstraintViolationException, which the Store API turns into a 400
        $this->validator->validate($data->all(), $definition);

        $productCriteria = (new Criteria([$productId]))->addFields(['parentId']);
        $product = $this->productRepository->search($productCriteria, $context)->getEntities()->first();

        if ($product === null) {
            throw ProductException::productNotFound($productId);
        }

        // The question waits, inactive and unanswered, until the shop owner answers it
        $this->faqRepository->create([[
            'productId' => $product->get('parentId') ?? $productId,
            'question' => $data->getString('question'),
            'active' => false,
        ]], $context->getContext());

        return new NoContentResponse();
    }
}
