<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer;

use HeyPanel\Core\PlatformRequest;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CustomerValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        if ($argument->getType() !== CustomerEntity::class) {
            return;
        }

        $loginRequired = $request->attributes->get(PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED);

        if ($loginRequired !== true) {
            $route = $request->attributes->get('_route');

            throw CustomerException::missingRouteAnnotation('LoginRequired', $route);
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CHANNEL_CONTEXT_OBJECT);
        if (!$context instanceof ChannelContext) {
            $route = $request->attributes->get('_route');

            throw CustomerException::missingRouteChannel($route);
        }

        yield $context->getCustomer();
    }
}
