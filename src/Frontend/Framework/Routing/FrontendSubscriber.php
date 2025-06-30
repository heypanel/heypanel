<?php declare(strict_types=1);

namespace HeyPanel\Frontend\Framework\Routing;

use HeyPanel\Core\ChannelRequest;
use HeyPanel\Core\Framework\Routing\Event\ChannelContextResolvedEvent;
use HeyPanel\Core\Framework\Routing\Exception\MemberNotLoggedInRoutingException;
use HeyPanel\Core\Framework\Routing\KernelListenerPriorities;
use HeyPanel\Core\Framework\Routing\RoutingException;
use HeyPanel\Core\Framework\Util\Random;
use HeyPanel\Core\PlatformRequest;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\Event\MemberLoginEvent;
use HeyPanel\Core\System\Customer\Event\MemberLogoutEvent;
use HeyPanel\Core\System\Customer\Exception\MemberNotLoggedInException;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 */
class FrontendSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly MaintenanceModeResolver $maintenanceModeResolver,
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['startSession', 40],
                ['maintenanceResolver'],
            ],
            KernelEvents::EXCEPTION => [
                ['customerNotLoggedInHandler'],
                ['maintenanceResolver'],
            ],
            KernelEvents::CONTROLLER => [
                ['preventPageLoadingFromXmlHttpRequest', KernelListenerPriorities::KERNEL_CONTROLLER_EVENT_SCOPE_VALIDATE],
            ],
            MemberLoginEvent::class => [
                'updateSessionAfterLogin',
            ],
            MemberLogoutEvent::class => [
                'updateSessionAfterLogout',
            ],
            ChannelContextResolvedEvent::class => [
                ['replaceContextToken'],
            ],
        ];
    }

    public function startSession(): void
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if (!$mainRequest) {
            return;
        }
        if (!$mainRequest->attributes->get(ChannelRequest::ATTRIBUTE_IS_CHANNEL_REQUEST)) {
            return;
        }

        if (!$mainRequest->hasSession()) {
            return;
        }

        $session = $mainRequest->getSession();

        if (!$session->isStarted()) {
            $session->setName('session-');
            $session->start();
            $session->set('sessionId', $session->getId());
        }

        $channelId = $mainRequest->attributes->get(PlatformRequest::ATTRIBUTE_CHANNEL_ID);
        if ($channelId === null) {
            $channelContext = $mainRequest->attributes->get(PlatformRequest::ATTRIBUTE_CHANNEL_CONTEXT_OBJECT);
            if ($channelContext instanceof ChannelContext) {
                $channelId = $channelContext->getChannelId();
            }
        }

        if ($this->shouldRenewToken($session, $channelId)) {
            $token = Random::getAlphanumericString(32);
            $session->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $token);
            $session->set(PlatformRequest::ATTRIBUTE_CHANNEL_ID, $channelId);
        }

        $contextToken = $session->get(PlatformRequest::HEADER_CONTEXT_TOKEN);
        $mainRequest->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $contextToken);

        $currentRequest = $this->requestStack->getCurrentRequest();
        if ($currentRequest && $mainRequest !== $currentRequest) {
            $currentRequest->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $contextToken);
        }
    }

    public function updateSessionAfterLogin(MemberLoginEvent $event): void
    {
        $token = $event->getContextToken();

        $this->updateSession($token);
    }

    public function updateSessionAfterLogout(): void
    {
        $newToken = Random::getAlphanumericString(32);

        $this->updateSession($newToken, true);
    }

    public function updateSession(string $token, bool $destroyOldSession = false): void
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if (!$mainRequest) {
            return;
        }
        if (!$mainRequest->attributes->get(ChannelRequest::ATTRIBUTE_IS_CHANNEL_REQUEST)) {
            return;
        }

        if (!$mainRequest->hasSession()) {
            return;
        }

        $session = $mainRequest->getSession();
        $session->migrate($destroyOldSession);
        $session->set('sessionId', $session->getId());

        $session->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $token);
        $mainRequest->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $token);
    }

    public function customerNotLoggedInHandler(ExceptionEvent $event): void
    {
        if (!$event->getRequest()->attributes->has(ChannelRequest::ATTRIBUTE_IS_CHANNEL_REQUEST)) {
            return;
        }

        if (!$this->shouldRedirectLoginPage($event->getThrowable())) {
            return;
        }

        $request = $event->getRequest();

        $parameters = [
            'redirectTo' => $request->attributes->get('_route'),
            'redirectParameters' => json_encode($request->attributes->get('_route_params'), \JSON_THROW_ON_ERROR),
        ];

        $redirectResponse = new RedirectResponse($this->router->generate('frontend.account.login.page', $parameters));

        $event->setResponse($redirectResponse);
    }

    public function maintenanceResolver(RequestEvent $event): void
    {
        if ($this->maintenanceModeResolver->shouldRedirect($event->getRequest())) {
            $event->setResponse(
                new RedirectResponse($this->router->generate('frontend.maintenance.page'), Response::HTTP_TEMPORARY_REDIRECT)
            );
        }
    }

    public function preventPageLoadingFromXmlHttpRequest(ControllerEvent $event): void
    {
        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $scope = $event->getRequest()->attributes->get(PlatformRequest::ATTRIBUTE_ROUTE_SCOPE, []);

        if (!\in_array(FrontendRouteScope::ID, $scope, true)) {
            return;
        }

        $isAllowed = $event->getRequest()->attributes->getBoolean('XmlHttpRequest');
        if ($isAllowed) {
            return;
        }

        throw RoutingException::accessDeniedForXmlHttpRequest();
    }

    // used to switch session token - when the context token expired
    public function replaceContextToken(ChannelContextResolvedEvent $event): void
    {
        $context = $event->getChannelContext();

        // only update session if token expired and switched
        if ($event->getUsedToken() === $context->getToken()) {
            return;
        }

        $this->updateSession($context->getToken());
    }

    private function shouldRenewToken(SessionInterface $session, ?string $channelId = null): bool
    {
        if (!$session->has(PlatformRequest::HEADER_CONTEXT_TOKEN) || $channelId === null) {
            return true;
        }

        if ($this->systemConfigService->get('core.systemWideLoginRegistration.isMemberBoundToChannel')) {
            return $session->get(PlatformRequest::ATTRIBUTE_CHANNEL_ID) !== $channelId;
        }

        return false;
    }

    private function shouldRedirectLoginPage(\Throwable $ex): bool
    {
        if ($ex instanceof MemberNotLoggedInRoutingException) {
            return true;
        }

        if ($ex instanceof MemberNotLoggedInException) {
            return true;
        }

        return false;
    }
}
