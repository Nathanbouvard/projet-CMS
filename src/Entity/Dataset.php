<?php

namespace App\Entity;

use App\Repository\DatasetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: DatasetRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Dataset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(inversedBy: 'datasets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $provider = null;

    #[ORM\OneToMany(mappedBy: 'dataset', targetEntity: DataColumn::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $dataColumns;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
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
            $dataColumn->setDataset($this);
        }

        return $this;
    }

    public function removeDataColumn(DataColumn $dataColumn): static
    {
        if ($this->dataColumns->removeElement($dataColumn)) {
            // set the owning side to null (unless already changed)
            if ($dataColumn->getDataset() === $this) {
                $dataColumn->setDataset(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}