<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Category;

use HeyPanel\Core\Content\Category\Exception\CategoryNotFoundException;
use HeyPanel\Core\Content\Cms\Exception\PageNotFoundException;
use HeyPanel\Core\Framework\HeyPanelHttpException;
use HeyPanel\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

class CategoryException extends HttpException
{
    public const SERVICE_CATEGORY_NOT_FOUND = 'CHECKOUT__SERVICE_CATEGORY_NOT_FOUND';
    public const FOOTER_CATEGORY_NOT_FOUND = 'CHECKOUT__FOOTER_CATEGORY_NOT_FOUND';
    public const AFTER_CATEGORY_NOT_FOUND = 'CONTENT__AFTER_CATEGORY_NOT_FOUND';

    public static function pageNotFound(string $pageId): HeyPanelHttpException
    {
        return new PageNotFoundException($pageId);
    }

    public static function categoryNotFound(string $id): HeyPanelHttpException
    {
        return new CategoryNotFoundException($id);
    }

    public static function serviceCategoryNotFoundForChannel(string $channelName): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::SERVICE_CATEGORY_NOT_FOUND,
            'Service category, for channel {{ channelName }}, is not set',
            ['channelName' => $channelName]
        );
    }

    public static function footerCategoryNotFoundForChannel(string $channelName): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::FOOTER_CATEGORY_NOT_FOUND,
            'Footer category, for channel {{ channelName }}, is not set',
            ['channelName' => $channelName]
        );
    }

    public static function afterCategoryNotFound(): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::AFTER_CATEGORY_NOT_FOUND,
            'Category to insert after not found.',
        );
    }
}
