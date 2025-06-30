<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Api\Context;

use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(typeProperty: 'type', mapping: ['system' => SystemSource::class, 'channel' => ChannelApiSource::class, 'admin-api' => AdminApiSource::class, 'client-api' => ClientApiSource::class, 'admin-channel-api' => AdminChannelApiSource::class])]
interface ContextSource
{
}
