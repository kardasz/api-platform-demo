<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\User\UserNotifyController;
use App\Entity\Traits\UuidTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"})
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"user:list", "uuid"}},
 *              "security"="is_granted('ROLE_MANAGER')",
 *         },
 *         "self_registration"={
 *             "method"="POST",
 *             "path"="/users/self-registration",
 *             "normalization_context"={"groups"={"user:details"}},
 *             "denormalization_context"={"groups"={"user:register"}},
 *             "openapi_context"={
 *                  "summary"="User self registration."
 *             }
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "normalization_context"={"groups"={"user:details", "uuid"}},
 *             "security"="is_granted('view', object)",
 *         },
 *         "user_activate"={
 *             "method"="PATCH",
 *             "path"="users/{id}/activate",
 *             "input"=false,
 *             "output"=false,
 *             "security"="is_granted('activate', object)",
 *             "openapi_context"={
 *                  "summary"="Activate user.",
 *                  "responses"={
 *                      "204" = {
 *                          "description" = "User activated"
 *                      },
 *                      "404" = {
 *                          "description" = "User not found"
 *                      }
 *                  }
 *             }
 *         },
 *         "user_deactivate"={
 *             "method"="PATCH",
 *             "path"="users/{id}/deactivate",
 *             "input"=false,
 *             "output"=false,
 *             "security"="is_granted('deactivate', object)",
 *             "openapi_context"={
 *                  "summary"="Deactivate user.",
 *                  "responses"={
 *                      "204" = {
 *                          "description" = "User deactivated"
 *                      },
 *                      "404" = {
 *                          "description" = "User not found"
 *                      }
 *                  }
 *             }
 *         },
 *         "user_notify"={
 *             "method"="POST",
 *             "path"="users/{id}/notify",
 *             "input"=false,
 *             "output"=false,
 *             "security"="is_granted('notify', object)",
 *             "controller"=UserNotifyController::class,
 *             "openapi_context"={
 *                  "summary"="Notify user.",
 *                  "responses"={
 *                      "204" = {
 *                          "description" = "User notified"
 *                      },
 *                      "404" = {
 *                          "description" = "User not found"
 *                      }
 *                  }
 *             }
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "normalization_context"={"groups"={"user:details"}},
 *             "denormalization_context"={"groups"={"user:edit"}},
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
class User implements UserInterface
{
    use UuidTrait;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\Email()
     * @Assert\NotBlank()
     * @Assert\NotNull()
     *
     * @Groups({"user:register", "user:restricted"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "example@email.com"
     *         }
     *     }
     * )
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=180, nullable=false)
     *
     * @Groups({"user:list", "user:details", "user:register", "user:edit"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "John"
     *         }
     *     }
     * )
     */
    private ?string $firstName;

    /**
     * @ORM\Column(type="string", length=180, nullable=false)
     *
     * @Groups({"user:list", "user:details", "user:register","user:edit"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = "Doe"
     *         }
     *     }
     * )
     */
    private ?string $lastName;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @SerializedName("password")
     *
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(min="8")
     * @Assert\Regex(
     *     pattern="/(?=.*\d)(?=.*\W+)(?=.*[A-Z])(?=.*[a-z])/",
     *     message="Password should contain at least 1 upper case letter and 1 lower case letter 1 number and 1 special character"
     * )
     * @Assert\NotCompromisedPassword()
     *
     * @Groups({"user:register","user:edit"})
     *
     * @ApiProperty(
     *     attributes={
     *         "openapi_context"={
     *             "format" = "string",
     *             "example" = ""
     *         }
     *     }
     * )
     */
    private ?string $plainPassword;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private bool $active = false;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?User $manager;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return $this
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
