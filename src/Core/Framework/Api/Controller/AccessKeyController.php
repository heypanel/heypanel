<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Controller;

use HeyPanel\Core\Framework\Api\Util\AccessKeyHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class AccessKeyController extends AbstractController
{
    #[Route(path: '/api/_action/access-key/intergration', name: 'api.action.access-key.integration', defaults: ['_acl' => ['api_action_access-key_integration']], methods: ['GET'])]
    public function generateIntegrationKey(): JsonResponse
    {
        return new JsonResponse([
            'accessKey' => AccessKeyHelper::generateAccessKey('integration'),
            'secretAccessKey' => AccessKeyHelper::generateSecretAccessKey(),
        ]);
    }

    #[Route(path: '/api/_action/access-key/user', name: 'api.action.access-key.user', methods: ['GET'])]
    public function generateUserKey(): JsonResponse
    {
        return new JsonResponse([
            'accessKey' => AccessKeyHelper::generateAccessKey('user'),
            'secretAccessKey' => AccessKeyHelper::generateSecretAccessKey(),
        ]);
    }

    #[Route(path: '/api/_action/access-key/channel', name: 'api.action.access-key.channel', methods: ['GET'])]
    public function generateSalesChannelKey(): JsonResponse
    {
        return new JsonResponse([
            'accessKey' => AccessKeyHelper::generateAccessKey('channel'),
        ]);
    }
}
