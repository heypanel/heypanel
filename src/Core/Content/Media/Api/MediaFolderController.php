<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Api;

use HeyPanel\Core\Content\Media\MediaFolderService;
use HeyPanel\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class MediaFolderController extends AbstractController
{
    /**
     * @internal
     */
    public function __construct(private readonly MediaFolderService $dissolveFolderService)
    {
    }

    #[Route(path: '/api/_action/media-folder/{folderId}/dissolve', name: 'api.action.media-folder.dissolve', methods: ['POST'])]
    public function dissolve(string $folderId, Context $context): Response
    {
        $this->dissolveFolderService->dissolve($folderId, $context);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
