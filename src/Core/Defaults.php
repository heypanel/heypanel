<?php declare(strict_types=1);

namespace HeyPanel\Core;

/**
 * @codeCoverageIgnore
 */
final class Defaults
{
    public const LANGUAGE_SYSTEM = '2fbb5fe2e29a4d70aa5854ce7ce3e20b';

    public const LIVE_VERSION = '0fa91ce3e96a4bc2be4bd9ce752c3425';
    public const STORAGE_DATE_TIME_FORMAT = 'Y-m-d H:i:s.v';

    /**
     * Don't depend on this being CNY, the underlying currency can be overwritten by the installer!
     */
    public const CURRENCY = 'b7d2554b0ce847cd82f3ac9bd1c0dfca';
    /**
     * Do not use STORAGE_DATE_FORMAT for createdAt fields, use STORAGE_DATE_TIME_FORMAT instead
     */
    public const STORAGE_DATE_FORMAT = 'Y-m-d';
    public const CHANNEL_TYPE_API = 'f183ee5650cf4bdb8a774337575067a6';

    public const CHANNEL_TYPE_FRONTEND = '8a243080f92e4c719546314b577cf82b';
    public const CMS_QUESTION_DETAIL_PAGE = '7a6d253a67204037966f42b0119704d5';
}
