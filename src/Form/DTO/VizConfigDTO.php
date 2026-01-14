<?php

namespace App\Form\DTO;

use App\Entity\Dataset;
use Symfony\Component\Validator\Constraints as Assert;

class VizConfigDTO
{
    // On stocke l'objet Dataset directement pour que le formulaire soit content
    #[Assert\NotNull(message: 'Le dataset est obligatoire')]
    public ?Dataset $dataset = null;

    #[Assert\NotBlank(message: 'Le type de graphique est obligatoire')]
    #[Assert\Choice(choices: ['bar', 'line', 'pie', 'scatter'], message: 'Type invalide')]
    public ?string $type = null;

    #[Assert\NotBlank(message: 'L\'axe X est obligatoire')]
    public ?string $axeX = null;

    #[Assert\NotBlank(message: 'L\'axe Y est obligatoire')]
    public ?string $axeY = null;

    #[Assert\NotBlank(message: 'La couleur est obligatoire')]
    public ?string $couleur = '#3498db';

    #[Assert\Length(min: 0, max: 50)]
    public ?string $titre = '';

    // Cette méthode servira à transformer l'objet en tableau pour la Base de Données
    public function toArray(): array
    {
        return [
            'dataset_id' => $this->dataset?->getId(),
            'type' => $this->type,
            'axe_x' => $this->axeX,
            'axe_y' => $this->axeY,
            'couleur' => $this->couleur,
            'titre' => $this->titre,
        ];
    }
}