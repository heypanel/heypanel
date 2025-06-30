<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Api;

use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Api\Context\AdminApiSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Criteria;
use HeyPanel\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use HeyPanel\Core\Framework\Store\Services\AbstractExtensionDataProvider;
use HeyPanel\Core\System\User\UserEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @internal
 */
#[Route(defaults: ['_routeScope' => ['api'], '_acl' => ['system.plugin_maintain']])]
class ExtensionStoreDataController extends AbstractController
{
    public function __construct(
        private readonly AbstractExtensionDataProvider $extensionDataProvider,
        private readonly EntityRepository $userRepository,
        private readonly EntityRepository $languageRepository
    ) {
    }

    #[Route(path: '/api/_action/extension/installed', name: 'api.extension.installed', methods: ['GET'])]
    public function getInstalledExtensions(Context $context): Response
    {
        $context = $this->switchContext($context);

        return new JsonResponse(
            $this->extensionDataProvider->getInstalledExtensions($context)
        );
    }

    private function switchContext(Context $context): Context
    {
        if (!$context->getSource() instanceof AdminApiSource) {
            return $context;
        }

        /** @var AdminApiSource $source */
        $source = $context->getSource();

        if ($source->getUserId() === null) {
            return $context;
        }

        $criteria = new Criteria([$source->getUserId()]);

        /** @var UserEntity|null $user */
        $user = $this->userRepository->search($criteria, $context)->first();

        if ($user === null) {
            return $context;
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('localeId', $user->getLocaleId()));
        $criteria->setLimit(1);
        $languageId = $this->languageRepository->searchIds($criteria, $context)->firstId();

        if ($languageId === null) {
            return $context;
        }

        return new Context(
            $context->getSource(),
            [$languageId, Defaults::LANGUAGE_SYSTEM]
        );
    }
}
