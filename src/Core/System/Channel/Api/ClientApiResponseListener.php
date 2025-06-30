<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Api;

use HeyPanel\Core\System\Channel\ClientApiResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ClientApiResponseListener implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly StructEncoder $encoder,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['encodeResponse', 10000],
        ];
    }

    public function encodeResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response instanceof ClientApiResponse) {
            return;
        }

        $this->dispatch($event);

        $includes = $event->getRequest()->get('includes', []);

        if (!\is_array($includes)) {
            $includes = explode(',', $includes);
        }

        $fields = new ResponseFields($includes);

        $encoded = $this->encoder->encode($response->getObject(), $fields);

        $jsonResponse = new JsonResponse(null, $response->getStatusCode(), $response->headers->all());
        $jsonResponse->setEncodingOptions(\JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_SLASHES);
        $jsonResponse->setData($encoded);

        $jsonResponse->setContent((string) $jsonResponse->getContent());

        $event->setResponse($jsonResponse);
    }

    /**
     * Equivalent to `\HeyPanel\Core\Framework\Routing\RouteEventSubscriber::render`
     */
    private function dispatch(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('_route')) {
            return;
        }

        $name = $request->attributes->get('_route') . '.encode';
        $this->dispatcher->dispatch($event, $name);
    }
}
