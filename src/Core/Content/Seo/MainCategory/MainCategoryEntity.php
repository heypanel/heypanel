<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Seo\MainCategory;

use HeyPanel\Core\Content\Category\CategoryEntity;
use HeyPanel\Core\Content\Post\QuestionEntity;
use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use HeyPanel\Core\System\Channel\ChannelEntity;

class MainCategoryEntity extends Entity
{
    use EntityIdTrait;

    protected string $channelId;

    protected ?ChannelEntity $channel = null;

    protected string $categoryId;

    protected string $categoryVersionId;

    protected ?CategoryEntity $category = null;

    protected string $questionId;

    protected ?QuestionEntity $question = null;

    public function getChannelId(): string
    {
        return $this->channelId;
    }

    public function setChannelId(string $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function getChannel(): ?ChannelEntity
    {
        return $this->channel;
    }

    public function setChannel(?ChannelEntity $channel): void
    {
        $this->channel = $channel;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(CategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function getCategoryVersionId(): string
    {
        return $this->categoryVersionId;
    }

    public function setCategoryVersionId(string $categoryVersionId): void
    {
        $this->categoryVersionId = $categoryVersionId;
    }

    public function getQuestionId(): string
    {
        return $this->questionId;
    }

    public function setQuestionId(string $questionId): void
    {
        $this->questionId = $questionId;
    }

    public function getQuestion(): ?QuestionEntity
    {
        return $this->question;
    }

    public function setQuestion(?QuestionEntity $question): void
    {
        $this->question = $question;
    }
}
