<?php

namespace App\Entity;

use App\Repository\EnclosRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnclosRepository::class)]
class Enclos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $superficie = null;

    #[ORM\Column]
    private ?int $nombre_max_animal = null;

    #[ORM\Column]
    private ?bool $quarantaine = null;

    #[ORM\ManyToOne(inversedBy: 'enclos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Espace $Espace = null;

    #[ORM\OneToMany(mappedBy: 'Enclos', targetEntity: Animal::class, orphanRemoval: false)]
    private Collection $animaux;

    public function __construct()
    {
        $this->animaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?int
    {
        return $this->superficie;
    }

    public function setSuperficie(int $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getNombreMaxAnimal(): ?int
    {
        return $this->nombre_max_animal;
    }

    public function setNombreMaxAnimal(int $nombre_max_animal): self
    {
        $this->nombre_max_animal = $nombre_max_animal;

        return $this;
    }

    public function isQuarantaine(): ?bool
    {
        return $this->quarantaine;
    }

    public function setQuarantaine(bool $quarantaine): self
    {
        $this->quarantaine = $quarantaine;

        return $this;
    }

    public function getEspace(): ?Espace
    {
        return $this->Espace;
    }

    public function setEspace(?Espace $Espace): self
    {
        $this->Espace = $Espace;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimal(): Collection
    {
        return $this->animaux;
    }

    public function addAnimal(Animal $animal): self
    {
        if (!$this->animaux->contains($animal)) {
            $this->animaux->add($animal);
            $animal->setEnclos($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): self
    {
        if ($this->animaux->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getEnclos() === $this) {
                $animal->setEnclos(null);
            }
        }

        return $this;
    }
}
