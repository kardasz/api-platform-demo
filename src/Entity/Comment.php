<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\UuidTrait;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ApiResource(
 *     iri="http://schema.org/Comment",
 *     normalizationContext={"groups"={"comment:list", "user:list", "uuid"}},
 *     collectionOperations={
 *         "post",
 *         "get"
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "security"="is_granted('view', object)",
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "security"="is_granted('delete', object)",
 *             "output"=false
 *         }
 *     }
 * )
 */
class Comment
{
    use UuidTrait;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min=100, max=500)
     *
     * @Groups({"comment:list", "comment:create"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "tag1"
     *         }
     *     }
     * )
     */
    private ?string $content = null;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     * @Assert\NotNull()
     *
     * @Groups({"comment:list", "comment:details"})
     */
    private ?Article $article;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?User $author;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

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
