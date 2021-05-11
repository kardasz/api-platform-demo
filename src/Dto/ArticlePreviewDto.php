<?php

namespace App\Dto;

use App\Entity\User;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class ArticlePreviewDto.
 */
class ArticlePreviewDto
{
    /**
     * @Groups({"article:preview","article:list","uuid"})
     */
    protected ?string $id;

    /**
     * @Groups({"article:preview","article:list"})
     */
    private ?string $title;

    /**
     * @Groups({"article:preview","article:list"})
     */
    private ?string $description;

    /**
     * @Groups({"article:preview","article:list"})
     */
    private ?DateTime $publishedAt;

    /**
     * @Groups({"article:preview","article:list"})
     */
    private ?User $author;

    public function __construct(
        ?string $id = null,
        ?string $title = null,
        ?string $description = null,
        ?DateTime $publishedAt = null,
        ?User $author = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->publishedAt = $publishedAt;
        $this->author = $author;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
