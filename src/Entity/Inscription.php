<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity="Employe")
     * @ORM\JoinColumn()
     */
    private $lEmploye;

    /**
     * @ORM\ManyToOne(targetEntity="Formation")
     * @ORM\JoinColumn()
     */
    private $laFormation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getLEmploye(): ?Employe
    {
        return $this->lEmploye;
    }

    public function setLEmploye(?Employe $lEmploye): self
    {
        $this->lEmploye = $lEmploye;

        return $this;
    }

    public function getLaFormation(): ?Formation
    {
        return $this->laFormation;
    }

    public function setLaFormation(?Formation $laFormation): self
    {
        $this->laFormation = $laFormation;

        return $this;
    }
}
