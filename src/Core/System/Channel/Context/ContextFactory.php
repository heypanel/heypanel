<?php

declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Api\Context\AdminChannelApiSource;
use HeyPanel\Core\Framework\Api\Context\ChannelApiSource;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Uuid\Uuid;
use HeyPanel\Core\System\Channel\ChannelException;
use HeyPanel\Core\System\Channel\Event\ContextCreatedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @final
 */
class ContextFactory
{
    /**
     * @internal
     */
    public function __construct(
        private Connection $connection,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param array{originalContext?: Context, version-id?: string, languageId?: string} $options
     */
    public function getContext(string $channelId, array $options): Context
    {
        $sql = '
        # context-factory::base-context

        SELECT
          channel.id as channel_id,
          channel.language_id as channel_default_language_id,
          channel.currency_id as channel_currency_id,
          currency.factor as channel_currency_factor,
          GROUP_CONCAT(LOWER(HEX(channel_language.language_id))) as channel_language_ids
        FROM channel
            INNER JOIN currency
                ON channel.currency_id = currency.id
            LEFT JOIN channel_language
                ON channel_language.channel_id = channel.id
        WHERE channel.id = :id
        GROUP BY channel.id, channel.language_id, channel.currency_id, currency.factor';

        $data = $this->connection->fetchAssociative($sql, [
            'id' => Uuid::fromHexToBytes($channelId),
        ]);
        if ($data === false) {
            throw ChannelException::noContextData($channelId);
        }

        if (isset($options[ChannelContextService::ORIGINAL_CONTEXT])) {
            $origin = new AdminChannelApiSource($channelId, $options[ChannelContextService::ORIGINAL_CONTEXT]);
        } else {
            $origin = new ChannelApiSource($channelId);
        }

        // explode all available languages for the provided channel
        $languageIds = $data['channel_language_ids'] ? explode(',', (string) $data['channel_language_ids']) : [];
        $languageIds = array_keys(array_flip($languageIds));

        // check which language should be used in the current request (request header set, or context already contains a language - stored in `channel_api_context`)
        $defaultLanguageId = Uuid::fromBytesToHex($data['channel_default_language_id']);

        $languageChain = $this->buildLanguageChain($options, $defaultLanguageId, $languageIds);

        $versionId = $options[ChannelContextService::VERSION_ID] ?? Defaults::LIVE_VERSION;

        return $this->eventDispatcher->dispatch(new ContextCreatedEvent(
            new Context(
                $origin,
                $languageChain,
                $versionId,
                true,
                Uuid::fromBytesToHex($data['channel_currency_id']),
                (float) $data['channel_currency_factor'],
            ),
        ))->context;
    }

    /**
     * @param array{originalContext?: Context, version-id?: string, languageId?: string} $sessionOptions
     * @param array<string> $availableLanguageIds
     *
     * @return non-empty-list<string>
     */
    private function buildLanguageChain(array $sessionOptions, string $defaultLanguageId, array $availableLanguageIds): array
    {
        $current = $sessionOptions[ChannelContextService::LANGUAGE_ID] ?? $defaultLanguageId;

        if (!\is_string($current) || !Uuid::isValid($current)) {
            throw ChannelException::invalidLanguageId();
        }

        // check provided language is part of the available languages
        if (!\in_array($current, $availableLanguageIds, true)) {
            throw ChannelException::providedLanguageNotAvailable($current, $availableLanguageIds);
        }

        if ($current === Defaults::LANGUAGE_SYSTEM) {
            return [Defaults::LANGUAGE_SYSTEM];
        }

        // provided language can be a child language
        return array_values(array_filter([$current, $this->getParentLanguageId($current), Defaults::LANGUAGE_SYSTEM]));
    }

    private function getParentLanguageId(string $languageId): ?string
    {
        $data = $this->connection->createQueryBuilder()
            ->select('LOWER(HEX(language.parent_id))')
            ->from('language')
            ->where('language.id = :id')
            ->setParameter('id', Uuid::fromHexToBytes($languageId))
            ->executeQuery()
            ->fetchOne();

        if ($data === false) {
            throw ChannelException::languageNotFound($languageId);
        }

        return $data;
    }
}
