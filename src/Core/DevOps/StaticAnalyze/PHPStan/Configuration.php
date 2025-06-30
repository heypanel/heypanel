<?php declare(strict_types=1);

namespace HeyPanel\Core\DevOps\StaticAnalyze\PHPStan;

/**
 * @internal
 */
final class Configuration
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array<string>
     */
    public function getAllowedNonDomainExceptions(): array
    {
        return $this->parameters['allowedNonDomainExceptions'] ?? [];
    }
}
