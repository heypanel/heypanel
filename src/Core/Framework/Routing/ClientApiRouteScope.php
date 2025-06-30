<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Routing;

use HeyPanel\Core\Framework\Api\ApiDefinition\DefinitionService;
use HeyPanel\Core\Framework\Api\Context\ChannelApiSource;
use HeyPanel\Core\Framework\Api\Context\SystemSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;

class ClientApiRouteScope extends AbstractRouteScope implements ChannelContextRouteScopeDependant
{
    final public const ID = DefinitionService::CLIENT_API;

    protected array $allowedPaths = [DefinitionService::CLIENT_API];

    public function isAllowed(Request $request): bool
    {
        if (!$request->attributes->get('auth_required', false)) {
            return true;
        }

        /** @var Context $requestContext */
        $requestContext = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);

        if (!$request->attributes->get('auth_required', true)) {
            return $requestContext->getSource() instanceof SystemSource;
        }

        return $requestContext->getSource() instanceof ChannelApiSource;
    }

    public function getId(): string
    {
        return static::ID;
    }
}
