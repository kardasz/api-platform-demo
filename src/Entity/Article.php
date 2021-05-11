<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Dto\ArticlePreviewDto;
use App\Entity\Traits\UuidTrait;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @UniqueEntity(fields={"title"})
 * @ApiResource(
 *     iri="http://schema.org/Comment",
 *     normalizationContext={"groups"={"article:list", "user:list", "uuid"}},
 *     collectionOperations={
 *         "post",
 *         "get",
 *         "preview"={
 *              "method"="GET",
 *              "output"=ArticlePreviewDto::class,
 *              "path"="articles/preview",
 *              "normalization_context"={"groups"={"article:preview", "user:list", "uuid"}},
 *          }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "security"="is_granted('view', object)",
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "security"="is_granted('update', object)",
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "security"="is_granted('delete', object)",
 *             "output"=false
 *         }
 *     }
 * )
 */
class Article
{
    use UuidTrait;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(max=180)
     *
     * @Groups({"article:list", "article:details", "article:create"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "Lorem ipsum dolor..."
     *         }
     *     }
     * )
     */
    private ?string $title = null;

    /**
     * @ORM\ManyToMany(targetEntity=MediaObject::class, cascade={"persist"})
     *
     * @Groups({"article:details", "article:create"})
     *
     * @Assert\Valid()
     */
    private Collection $mediaObjects;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, cascade={"persist"})
     *
     * @Groups({"article:details", "article:create"})
     *
     * @Assert\Valid()
     */
    private Collection $tags;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, cascade={"persist"}, mappedBy="article")
     *
     * @ApiSubresource()
     */
    private Collection $comments;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"article:details", "article:create"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "Lorem ipsum dolor..."
     *         }
     *     }
     * )
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"article:details", "article:create"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "datetime",
     *             "example" = "2020-07-07"
     *         }
     *     }
     * )
     */
    private ?DateTime $publishedAt = null;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"article:details","article:list", "article:create"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "draft|pulished|trash"
     *         }
     *     }
     * )
     */
    private ?string $status = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     * @Groups({"article:details","article:list"})
     */
    private ?User $author;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->mediaObjects = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getMediaObjects(): Collection
    {
        return $this->mediaObjects;
    }

    public function setMediaObjects(Collection $mediaObjects)
    {
        $this->mediaObjects = $mediaObjects;

        return $this;
    }

    public function addMediaObject(MediaObject ...$mediaObjects): self
    {
        foreach ($mediaObjects as $mediaObject) {
            if (!$this->mediaObjects->contains($mediaObject)) {
                $this->mediaObjects[] = $mediaObject;
            }
        }

        return $this;
    }

    public function addMediaObjects(array $mediaObjects): self
    {
        return $this->addMediaObject(...$mediaObjects);
    }

    public function removeMediaObject(MediaObject $mediaObject): self
    {
        if ($this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->removeElement($mediaObject);
        }

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function setTags(Collection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function addTag(Tag ...$tags): self
    {
        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->tags[] = $tag;
            }
        }

        return $this;
    }

    public function addTags(array $tags): self
    {
        return $this->addTag(...$tags);
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Collection $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    public function addComment(Comment ...$comments): self
    {
        foreach ($comments as $comment) {
            if (!$this->comments->contains($comment)) {
                $comment->setArticle($this);
                $this->comments[] = $comment;
            }
        }

        return $this;
    }

    public function addComments(array $comments): self
    {
        return $this->addComment(...$comments);
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $comment->setArticle(null);
            $this->comments->removeElement($comment);
        }

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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
