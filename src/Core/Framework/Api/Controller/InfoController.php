<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Controller;

use Doctrine\DBAL\Connection;
use HeyPanel\Administration\Framework\Twig\ViteFileAccessorDecorator;
use HeyPanel\Core\Framework\Api\ApiDefinition\DefinitionService;
use HeyPanel\Core\Framework\Api\ApiDefinition\Generator\EntitySchemaGenerator;
use HeyPanel\Core\Framework\Api\ApiDefinition\Generator\OpenApi3Generator;
use HeyPanel\Core\Framework\Api\Route\ApiRouteInfoResolver;
use HeyPanel\Core\Framework\Api\Route\RouteInfo;
use HeyPanel\Core\Framework\Bundle;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Increment\Exception\IncrementGatewayNotFoundException;
use HeyPanel\Core\Framework\Increment\IncrementGatewayRegistry;
use HeyPanel\Core\Framework\MessageQueue\Stats\StatsService;
use HeyPanel\Core\Framework\Plugin;
use HeyPanel\Core\Kernel;
use HeyPanel\Core\Maintenance\System\Service\AppUrlVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[Route(defaults: ['_routeScope' => ['api']])]
class InfoController extends AbstractController
{
    private const API_SCOPE_ADMIN = 'api';

    /**
     * @internal
     */
    public function __construct(
        private readonly DefinitionService $definitionService,
        private readonly ParameterBagInterface $params,
        private readonly Kernel $kernel,
        private readonly IncrementGatewayRegistry $incrementGatewayRegistry,
        private readonly Connection $connection,
        private readonly AppUrlVerifier $appUrlVerifier,
        private readonly RouterInterface $router,
        private readonly ApiRouteInfoResolver $apiRouteInfoResolver,
        private readonly ?ViteFileAccessorDecorator $viteFileAccessorDecorator,
        private readonly Filesystem $filesystem,
        private readonly StatsService $messageStatsService,
    ) {
    }

    #[Route(path: '/api/_info/queue.json', name: 'api.info.queue', methods: ['GET'])]
    public function queue(): JsonResponse
    {
        try {
            $gateway = $this->incrementGatewayRegistry->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL);
        } catch (IncrementGatewayNotFoundException) {
            // In case message_queue pool is disabled
            return new JsonResponse([]);
        }

        // Fetch unlimited message_queue_stats
        $entries = $gateway->list('message_queue_stats', -1);

        return new JsonResponse(array_map(static fn (array $entry) => [
            'name' => $entry['key'],
            'size' => (int) $entry['count'],
        ], array_values($entries)));
    }

    #[Route(path: '/api/_info/message-stats.json', name: 'api.info.message-stats', methods: ['GET'])]
    public function messageStats(): JsonResponse
    {
        $response = new JsonResponse();
        $response->setEncodingOptions($response->getEncodingOptions() | \JSON_PRESERVE_ZERO_FRACTION);
        $response->setData($this->messageStatsService->getStats());

        return $response;
    }

    #[Route(
        path: '/api/_info/open-api-schema.json',
        name: 'api.info.open-api-schema',
        defaults: ['auth_required' => '%heypanel.api.api_browser.auth_required_str%'],
        methods: ['GET']
    )]
    public function openApiSchema(): JsonResponse
    {
        $data = $this->definitionService->getSchema(OpenApi3Generator::FORMAT);

        return new JsonResponse($data);
    }

    #[Route(path: '/api/_info/entity-schema.json', name: 'api.info.entity-schema', methods: ['GET'])]
    public function entitySchema(): JsonResponse
    {
        $data = $this->definitionService->getSchema(EntitySchemaGenerator::FORMAT);

        return new JsonResponse($data);
    }

    #[Route(path: '/api/_info/config', name: 'api.info.config', methods: ['GET'])]
    public function config(Context $context, Request $request): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->getHeyPanelVersion(),
            'versionRevision' => $this->params->get('kernel.heypanel_version_revision'),
            'adminWorker' => [
                'enableAdminWorker' => $this->params->get('heypanel.admin_worker.enable_admin_worker'),
                'enableQueueStatsWorker' => $this->params->get('heypanel.admin_worker.enable_queue_stats_worker'),
                'enableNotificationWorker' => $this->params->get('heypanel.admin_worker.enable_notification_worker'),
                'transports' => $this->params->get('heypanel.admin_worker.transports'),
            ],
            'bundles' => $this->getBundles(),
            'settings' => [
                'enableUrlFeature' => $this->params->get('heypanel.media.enable_url_upload_feature'),
                'appUrlReachable' => $this->appUrlVerifier->isAppUrlReachable($request),
                'private_allowed_extensions' => $this->params->get('heypanel.filesystem.private_allowed_extensions'),
                'enableHtmlSanitizer' => $this->params->get('heypanel.html_sanitizer.enabled'),
                'disableExtensionManagement' => !$this->params->get('heypanel.deployment.runtime_extension_management'),
            ],
        ]);
    }

    #[Route(path: '/api/_info/version', name: 'api.info.heypanel.version', methods: ['GET'])]
    #[Route(path: '/api/v1/_info/version', name: 'api.info.heypanel.version_old_version', methods: ['GET'])]
    public function infoHeyPanelVersion(): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->getHeyPanelVersion(),
        ]);
    }

    #[Route(
        path: '/api/_info/routes',
        name: 'api.info.routes',
        defaults: ['auth_required' => '%heypanel.api.api_browser.auth_required_str%'],
        methods: ['GET']
    )]
    public function getRoutes(): JsonResponse
    {
        $endpoints = array_map(
            static fn (RouteInfo $endpoint) => ['path' => $endpoint->path, 'methods' => $endpoint->methods],
            $this->apiRouteInfoResolver->getApiRoutes(self::API_SCOPE_ADMIN)
        );

        return new JsonResponse(['endpoints' => $endpoints]);
    }

    /**
     * @return array<string, array{
     *     type: 'plugin',
     *     css: list<string>,
     *     js: list<string>,
     *     baseUrl: ?string
     * }|array{
     *     type: 'app',
     *     name: string,
     *     active: bool,
     *     integrationId: string,
     *     baseUrl: string,
     *     version: string,
     *     permissions: array<string, list<string>>
     * }>
     */
    private function getBundles(): array
    {
        $assets = [];

        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof Bundle) {
                continue;
            }

            if (!$this->viteFileAccessorDecorator) {
                // Admin bundle is not there, admin assets are not available
                continue;
            }

            $viteEntryPoints = $this->viteFileAccessorDecorator->getBundleData($bundle);

            $technicalBundleName = $this->getTechnicalBundleName($bundle);
            $styles = $viteEntryPoints['entryPoints'][$technicalBundleName]['css'] ?? [];
            $scripts = $viteEntryPoints['entryPoints'][$technicalBundleName]['js'] ?? [];
            $baseUrl = $this->getBaseUrl($bundle);

            if (empty($styles) && empty($scripts) && $baseUrl === null) {
                continue;
            }

            $assets[$bundle->getName()] = [
                'css' => $styles,
                'js' => $scripts,
                'baseUrl' => $baseUrl,
                'type' => 'plugin',
            ];
        }

        return $assets;
    }

    private function getBaseUrl(Bundle $bundle): ?string
    {
        if (!$bundle instanceof Plugin) {
            return null;
        }

        if ($bundle->getAdminBaseUrl()) {
            return $bundle->getAdminBaseUrl();
        }

        if (!$this->filesystem->exists($bundle->getPath() . '/Resources/public/meteor-app/index.html')) {
            return null;
        }

        // exception is possible as the administration is an optional dependency
        try {
            return $this->router->generate(
                'administration.plugin.index',
                [
                    'pluginName' => \mb_strtolower($bundle->getName()),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        } catch (\Throwable) {
            return null;
        }
    }

    private function getHeyPanelVersion(): string
    {
        $heypanelVersion = $this->params->get('kernel.heypanel_version');
        if ($heypanelVersion === Kernel::HEYADMIN_FALLBACK_VERSION) {
            $heypanelVersion = str_replace('.9999999-dev', '.9999999.9999999-dev', $heypanelVersion);
        }

        return $heypanelVersion;
    }

    private function getTechnicalBundleName(Bundle $bundle): string
    {
        return str_replace('_', '-', $bundle->getContainerPrefix());
    }
}
