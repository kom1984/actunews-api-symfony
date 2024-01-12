<?php

namespace App\Entity;


use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource]
//#[Get]
//#[Patch(security: "is_granted('ROLE_ADMIN') or object.user == user")]
//#[GetCollection]
#[Put(security: "is_granted('ROLE_ADMIN') or object.user == user")]
//#[Delete(security: "is_granted('ROLE_ADMIN') or object.user == user")]
//#[\ApiPlatform\Metadata\Post(security: "is_granted('ROLE_REPORTER')")]

#[\ApiPlatform\Metadata\Post(securityPostDenormalize: "is_granted('post_new', object)")]
#[Put(security: "is_granted('comment_edit', object)")]
#[Delete(security: "is_granted('comment_delete', object)")]

#[ApiResource(
    paginationItemsPerPage: 5,
    //normalizationContext: ['groups' => ['read:post:collection']]
)]

#[GetCollection(normalizationContext: ['groups' => ['read:post:collection']])]
#[Get(normalizationContext: ['groups' => ['read:post:item']])]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;



    #[ORM\Column(length: 255)]
    #[Groups(['read:post:collection','read:post:item'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:post:collection'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read:post:collection','read:post:item'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:post:collection','read:post:item'])]
    private ?string $image = null;

    #[ORM\Column]
    #[Groups(['read:post:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['read:post:collection','read:post:item'])]
    public ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['read:post:collection','read:post:item'])]
    private ?Category $category = null;

    public function __construct()
    {
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }


}
