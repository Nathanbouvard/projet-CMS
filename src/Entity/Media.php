<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['media:read']]),
        new GetCollection(normalizationContext: ['groups' => ['media:read']])
    ],
    normalizationContext: ['groups' => ['media:read']],
    denormalizationContext: ['groups' => ['media:write']]
)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['media:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read', 'block:read', 'media:read', 'media:write'])]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read', 'block:read', 'media:read', 'media:write'])]
    private ?string $altText = null;

    #[ORM\Column(length: 255)]
    #[Groups(['media:read', 'media:write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['media:read'])]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['media:read'])]
    private ?User $provider = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: DataColumn::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['media:read'])]
    private Collection $dataColumns;

    #[ORM\Column(length: 255)]
    #[Groups(['media:read'])]
    private ?string $mimeType = null;

    public function __construct()
    {
        $this->dataColumns = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setUploadedAtValue(): void
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(?string $altText): static
    {
        $this->altText = $altText;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }

    public function getProvider(): ?User
    {
        return $this->provider;
    }

    public function setProvider(?User $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return Collection<int, DataColumn>
     */
    public function getDataColumns(): Collection
    {
        return $this->dataColumns;
    }

    public function addDataColumn(DataColumn $dataColumn): static
    {
        if (!$this->dataColumns->contains($dataColumn)) {
            $this->dataColumns->add($dataColumn);
            $dataColumn->setMedia($this);
        }

        return $this;
    }

    public function removeDataColumn(DataColumn $dataColumn): static
    {
        if ($this->dataColumns->removeElement($dataColumn)) {
            // set the owning side to null (unless already changed)
            if ($dataColumn->getMedia() === $this) {
                $dataColumn->setMedia(null);
            }
        }

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->filename;
    }
}
