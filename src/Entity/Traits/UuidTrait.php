<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait UuidTrait.
 */
trait UuidTrait
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @Groups({"uuid"})
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     *
     * @Assert\Uuid()
     *
     * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "example" = "123e4567-e89b-12d3-a456-426655440000",
     *             "format" = "uuid"
     *         }
     *     },
     *     identifier=true
     * )
     */
    protected $id;

    public function getId(): ?string
    {
        return $this->id;
    }
}
