<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ]
)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['article:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Groups(['article:read'])]
    private ?string $titleColor = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Groups(['article:read'])]
    private ?string $textColor = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Groups(['article:read'])]
    private ?string $chartColor = null;

    #[ORM\Column(length: 255)]
    #[Groups(['article:read'])]
    private ?string $fontFamily = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['article:read'])]
    private ?string $imageSize = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['article:read'])]
    private ?string $fontSize = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getImageSize(): ?string
    {
        return $this->imageSize;
    }

    public function setImageSize(?string $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getFontSize(): ?string
    {
        return $this->fontSize;
    }

    public function setFontSize(?string $fontSize): static
    {
        $this->fontSize = $fontSize;

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

    public function getTitleColor(): ?string
    {
        return $this->titleColor;
    }

    public function setTitleColor(?string $titleColor): static
    {
        $this->titleColor = $titleColor;
        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): static
    {
        $this->textColor = $textColor;
        return $this;
    }

    public function getChartColor(): ?string
    {
        return $this->chartColor;
    }

    public function setChartColor(?string $chartColor): static
    {
        $this->chartColor = $chartColor;
        return $this;
    }

    public function getFontFamily(): ?string
    {
        return $this->fontFamily;
    }

    public function setFontFamily(string $fontFamily): static
    {
        $this->fontFamily = $fontFamily;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}