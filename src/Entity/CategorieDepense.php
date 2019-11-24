<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieDepenseRepository")
 * @ORM\Table(name="categorieDepenses")
 */
class CategorieDepense
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="App\Entity\Depense", mappedBy="categorieDepense")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depense", mappedBy="categorieDepense")
     * @ORM\JoinColumn(name="categorieDepense_id", referencedColumnName="id")
     */
    private $categorieDepenses;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getTypeProduit(): Collection
    {
        return $this->typeProduit;
    }

    /**
     * @return Collection|CategorieDepense[]
     */
    public function getCategorieDepenses(): Collection
    {
        return $this->categorieDepenses;
    }

    public function addCategorieDepense(CategorieDepense $categorieDepense): self
    {
        if (!$this->categorieDepenses->contains($categorieDepense)) {
            $this->categorieDepenses[] = $categorieDepense;
            $categorieDepense->setCategorieDepense($this);
        }

        return $this;
    }

    public function removeCategorieDepense(CategorieDepense $categorieDepense): self
    {
        if ($this->categorieDepenses->contains($categorieDepense)) {
            $this->categorieDepenses->removeElement($categorieDepense);
            // set the owning side to null (unless already changed)
            if ($categorieDepense->getCategorieDepense() === $this) {
                $categorieDepense->setCategorieDepense(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return "L'identifiant est :".$this->getId()." et le libelle est :".$this->getLibelle();
    }
}
