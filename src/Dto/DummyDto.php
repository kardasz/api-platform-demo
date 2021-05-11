<?php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DummyDto.
 *
 * @ApiResource(
 *     iri="http://schema.org/Dummy",
 *     shortName="Dummy",
 *     normalizationContext={"groups"={"dummy:list"}},
 *     collectionOperations={
 *         "post"={
 *             "denormalization_context"={"groups"={"dummy:create"}},
 *         },
 *         "get"
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"dummy:details"}},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "normalization_context"={"groups"={"dummy:details"}},
 *             "denormalization_context"={"groups"={"dummy:update"}},
 *         },
 *         "delete"
 *     }
 * )
 */
class DummyDto
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"dummy:list","dummy:details"})
     */
    private ?string $id = null;

    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(max=180)
     *
     * @Groups({"dummy:create","dummy:update","dummy:list","dummy:details"})
     */
    private ?string $title = null;

    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"dummy:create","dummy:update","dummy:list","dummy:details"})
     */
    private ?string $intro = null;

    /**
     * @Groups({"dummy:create", "dummy:update","dummy:details"})
     */
    private ?string $details = null;

    /**
     * @Groups({"dummy:create", "dummy:update","dummy:list","dummy:details"})
     */
    private ?DateTime $publishedAt;

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

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

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
}
