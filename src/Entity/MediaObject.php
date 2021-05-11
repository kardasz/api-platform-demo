<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\MediaObject\CreateMediaObjectController;
use App\Entity\Traits\UuidTrait;
use App\Repository\MediaObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MediaObjectRepository::class)
 * @Vich\Uploadable
 * @ApiResource(
 *     iri="http://schema.org/MediaObject",
 *     normalizationContext={"groups"={"media:details", "user:list", "uuid"}},
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateMediaObjectController::class,
 *             "deserialize"=false,
 *             "security"="is_granted('ROLE_USER')",
 *             "openapi_context"={
 *                 "summary"="Upload a media file.",
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"={
 *              "method"="GET",
 *              "security"="is_granted('ROLE_USER')",
 *         },
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
class MediaObject
{
    use UuidTrait;
    use TimestampableEntity;

    /**
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({"media:list", "media:details"})
     */
    private ?string $contentUrl = null;

    /**
     * @Assert\NotNull()
     * @Assert\File(
     *     maxSize = "100M",
     *     mimeTypes = {
     *      "application/pdf",
     *      "application/msword",
     *      "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *      "application/vnd.ms-powerpoint",
     *      "application/vnd.openxmlformats-officedocument.presentationml.presentation",
     *      "application/zip",
     *      "image/png",
     *      "image/jpeg",
     *     },
     *     mimeTypesMessage = "Please upload a valid pdf/doc/docx/ppt/pptx/zip/png/jpeg"
     * )
     * @Vich\UploadableField(
     *     mapping="media_objects",
     *     fileNameProperty="fileName"
     * )
     */
    private ?File $file = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"media:list", "media:details"})
     */
    private ?string $fileName = null;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups({"media:list", "media:details"})
     */
    private ?User $owner = null;

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): self
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
