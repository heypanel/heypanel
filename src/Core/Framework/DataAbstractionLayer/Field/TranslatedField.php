<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Field;

use HeyPanel\Core\Framework\DataAbstractionLayer\Dbal\FieldResolver\TranslationFieldResolver;
use HeyPanel\Core\Framework\DataAbstractionLayer\FieldSerializer\TranslatedFieldSerializer;
use HeyPanel\Core\System\Language\LanguageDefinition;

class TranslatedField extends Field
{
    final public const PRIORITY = 100;

    private readonly string $foreignClassName;

    private readonly string $foreignFieldName;

    public function __construct(string $propertyName)
    {
        $this->foreignClassName = LanguageDefinition::class;
        $this->foreignFieldName = 'id';

        parent::__construct($propertyName);
    }

    public function getExtractPriority(): int
    {
        return self::PRIORITY;
    }

    public function getForeignClassName(): string
    {
        return $this->foreignClassName;
    }

    public function getForeignFieldName(): string
    {
        return $this->foreignFieldName;
    }

    protected function getSerializerClass(): string
    {
        return TranslatedFieldSerializer::class;
    }

    protected function getResolverClass(): ?string
    {
        return TranslationFieldResolver::class;
    }
}
