<?php

namespace App\Entity;

use App\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlockRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['block:read']]),
        new GetCollection(normalizationContext: ['groups' => ['block:read']])
    ],
    normalizationContext: ['groups' => ['block:read']],
    denormalizationContext: ['groups' => ['block:write']]
)]
class Block
{
    public const BLOCK_TYPE_TEXT = 'text';
    public const BLOCK_TYPE_IMAGE = 'image';
    public const BLOCK_TYPE_CHART = 'chart';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read', 'block:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['article:read', 'block:read', 'block:write'])]
    private ?string $type = null; 

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['article:read', 'block:read', 'block:write'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['article:read', 'block:read', 'block:write'])]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['block:read', 'block:write'])]
    private ?Article $article = null;

    #[ORM\ManyToOne(cascade: ["remove"])]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[Groups(['article:read', 'block:read', 'block:write'])]
    private ?Media $media = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['article:read', 'block:read', 'block:write'])]
    private ?array $vizConfig = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;
        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;
        return $this;
    }

    public function getVizConfig(): ?array
    {
        return $this->vizConfig;
    }

    public function setVizConfig(?array $vizConfig): static
    {
        $this->vizConfig = $vizConfig;
        return $this;
    }


}