<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DepenseRepository")
 * @ORM\Table(name="depenses")
 */
class Depense
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategorieDepense", inversedBy="depenses")
     */
    private $categorieDepense_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_depense;

    public function __construct()
    {
        $this->categorieDepense_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategorieDepense(): ?CategorieDepense
    {
        return $this->categorieDepense_id;
    }

    public function setCategorieDepense(?CategorieDepense $categorieDepense): self
    {
        $this->categorieDepense_id = $categorieDepense;

        return $this;
    }

    public function getDateDepense(): ?\DateTimeInterface
    {
        return $this->date_depense;
    }

    public function setDateDepense(?\DateTimeInterface $date_depense): self
    {
        $this->date_depense = $date_depense;

        return $this;
    }

    public function __toString() {

        return "montant: ".$this->getMontant()." description: ".$this->getDescription()." date:".$this->getDateDepense()." categorie: ".$this->getCategorieDepense().$this->__toString();
    }

}
