<?php declare(strict_types=1);

namespace HeyPanel\Core;

final class PlatformRequest
{
    /**
     * Response Headers
     */
    public const HEADER_FRAME_OPTIONS = 'x-frame-options';
    /**
     * Context attributes
     */
    public const ATTRIBUTE_CONTEXT_OBJECT = 'sw-context';
    public const ATTRIBUTE_CHANNEL_CONTEXT_OBJECT = 'sw-channel-context';
    public const ATTRIBUTE_ACL = '_acl';
    public const ATTRIBUTE_ROUTE_SCOPE = '_routeScope';
    public const HEADER_INDEXING_BEHAVIOR = 'indexing-behavior';
    public const ATTRIBUTE_CHANNEL_ID = 'sw-channel-id';
    public const ATTRIBUTE_ENTITY = '_entity';
    public const ATTRIBUTE_NO_STORE = '_noStore';
    public const ATTRIBUTE_HTTP_CACHE = '_httpCache';

    public const ATTRIBUTE_LOGIN_REQUIRED = '_loginRequired';
    public const ATTRIBUTE_CONTEXT_TOKEN_REQUIRED = '_contextTokenRequired';
    public const ATTRIBUTE_IS_ALLOWED_IN_MAINTENANCE = 'allow_maintenance';

    /**
     * Context headers
     */
    public const HEADER_CONTEXT_TOKEN = 'sw-context-token';
    public const HEADER_ACCESS_KEY = 'sw-access-key';
    public const HEADER_LANGUAGE_ID = 'sw-language-id';
    public const HEADER_INHERITANCE = 'sw-inheritance';
    public const HEADER_VERSION_ID = 'sw-version-id';
    public const HEADER_INCLUDE_SEO_URLS = 'sw-include-seo-urls';

    public const HEADER_INDEXING_SKIP = 'indexing-skip';
    public const HEADER_FORCE_CACHE_INVALIDATE = 'sw-force-cache-invalidate';

    /**
     * CSP
     */
    public const ATTRIBUTE_CSP_NONCE = '_cspNonce';

    /**
     * OAuth attributes
     */
    public const ATTRIBUTE_OAUTH_ACCESS_TOKEN_ID = 'oauth_access_token_id';
    public const ATTRIBUTE_OAUTH_CLIENT_ID = 'oauth_client_id';
    public const ATTRIBUTE_OAUTH_USER_ID = 'oauth_user_id';
    public const ATTRIBUTE_OAUTH_SCOPES = 'oauth_scopes';
}
