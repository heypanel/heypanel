<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Event;

#[IsFlowEventAware]
interface LanguageAware
{
    public const LANGUAGE_ID = 'languageId';

    public function getLanguageId(): ?string;
}
