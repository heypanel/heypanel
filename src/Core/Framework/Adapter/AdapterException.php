<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Adapter;

use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Source;

class AdapterException extends HttpException
{
    final public const INVALID_ARGUMENT = 'FRAMEWORK__INVALID_ARGUMENT_EXCEPTION';
    final public const CACHE_DIRECTORY_ERROR = 'FRAMEWORK__CACHE_DIRECTORY_ERROR';
    final public const FILESYSTEM_FACTORY_NOT_FOUND = 'FRAMEWORK__FILESYSTEM_FACTORY_NOT_FOUND';
    final public const CURRENCY_FILTER_MISSING_CONTEXT = 'FRAMEWORK__CURRENCY_FILTER_MISSING_CONTEXT';
    final public const CURRENCY_FILTER_MISSING_ISO_CODE = 'FRAMEWORK__CURRENCY_FILTER_MISSING_ISO_CODE';
    public const MISSING_EXTENDING_TWIG_TEMPLATE = 'FRAMEWORK__MISSING_EXTENDING_TWIG_TEMPLATE';
    public const TEMPLATE_SW_USE_SYNTAX_ERROR = 'FRAMEWORK__TEMPLATE_SW_USE_SYNTAX_ERROR';

    public static function swUseSyntaxError(int $line, Source $context): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::TEMPLATE_SW_USE_SYNTAX_ERROR,
            'The template references in a "sw_use" statement must be a string.',
            ['line' => $line, 'context' => $context]
        );
    }

    public static function missingExtendsTemplate(string $template): self
    {
        return new self(
            Response::HTTP_NOT_ACCEPTABLE,
            self::MISSING_EXTENDING_TWIG_TEMPLATE,
            'Template "{{ template }}" does not have an extending template.',
            [
                'template' => $template,
            ],
        );
    }

    public static function currencyFilterMissingIsoCode(): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::CURRENCY_FILTER_MISSING_ISO_CODE,
            'Error while processing Twig currency filter. Could not resolve currencyIsoCode.'
        );
    }

    public static function currencyFilterMissingContext(): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::CURRENCY_FILTER_MISSING_CONTEXT,
            'Error while processing Twig currency filter. No context or locale given.'
        );
    }

    public static function filesystemFactoryNotFound(string $type): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::FILESYSTEM_FACTORY_NOT_FOUND,
            'Filesystem factory for type "{{ type }}" not found.',
            ['type' => $type]
        );
    }

    public static function invalidArgument(string $message): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::INVALID_ARGUMENT,
            $message
        );
    }

    public static function cacheDirectoryError(string $directory): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::CACHE_DIRECTORY_ERROR,
            'Unable to write in the "{{ directory }}" directory',
            ['directory' => $directory]
        );
    }
}
