<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\UuidTrait;
use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @UniqueEntity(fields={"name"})
 * @ApiResource(
 *     iri="http://schema.org/Tag",
 *     normalizationContext={"groups"={"tag:list", "uuid"}},
 *     denormalizationContext={"groups"={"tag:create"}},
 *     collectionOperations={
 *         "post",
 *         "get"
 *     },
 *     itemOperations={
 *         "get",
 *         "delete"={
 *             "method"="DELETE",
 *             "security"="is_granted('ROLE_ADMIN')",
 *             "output"=false
 *         }
 *     }
 * )
 */
class Tag
{
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min=3, max=180)
     *
     * @Groups({"tag:list", "tag:create"})
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
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
