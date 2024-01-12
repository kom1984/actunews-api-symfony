<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\MeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
//#[ApiResource]
#[ApiResource(security: "is_granted('ROLE_USER')")]
#[Get]
#[Patch(security: "is_granted('ROLE_ADMIN') or object == user")]
#[GetCollection( uriTemplate: "/me",
    controller: MeController::class,
    security: "is_granted('ROLE_USER')",
    name: 'me')]
#[\ApiPlatform\Metadata\Post(security: "is_granted('ROLE_ADMIN')")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(['message'=>'Le champ "{{ label }}" est obligatoire.'])]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Votre e-mail ne peux pas dépasser {{ limit }} caractères.',
    )]
    #[Assert\Email(
        message: 'Vérifiez le format de votre e-mail.',
    )]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(['message'=>'Le champ "{{ label }}" est obligatoire.'])]
    #[Assert\PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_STRONG,
        'message' => 'Votre mot de passe est trop simple. Utilisez un mot de passe plus complexe pour votre sécurité.'
    ])]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(['message'=>'Le champ "{{ label }}" est obligatoire.'])]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Votre prénom ne peux pas dépasser {{ limit }} caractères.',
    )]
    #[Groups(['read:category:collection','read:post:item'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(['message'=>'Le champ "{{ label }}" est obligatoire.'])]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Votre nom ne peux pas dépasser {{ limit }} caractères.',
    )]
    #[Groups(['read:category:collection','read:post:item'])]
    private ?string $lastName = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class)]
    private Collection $posts;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
        $this->posts = new ArrayCollection();
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(string $firstName,
                                  string $lastName,
                                  string $email,
                                  string $password,
                                  array $roles = ['ROLE_USER']): self
    {
        $object = new self();
        $object->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPassword($password)
            ->setRoles($roles);

        return $object;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }
}
