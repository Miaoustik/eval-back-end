<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
    private ?Annonce $annonce = null;

    #[ORM\Column]
    private ?bool $allowed = false;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProfilCandidat $profilCandidat = null;

    public function __construct(ProfilCandidat $profilCandidat, Annonce $annonce)
    {
        $this->profilCandidat = $profilCandidat;
        $this->annonce = $annonce;
        $profilCandidat->addCandidature($this);
        $annonce->addCandidature($this);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Candidature
     */
    public function setId(?int $id): Candidature
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ProfilCandidat|null
     */
    public function getProfilCandidat(): ?ProfilCandidat
    {
        return $this->profilCandidat;
    }

    /**
     * @param ProfilCandidat|null $profilCandidat
     * @return Candidature
     */
    public function setProfilCandidat(?ProfilCandidat $profilCandidat): Candidature
    {
        $this->profilCandidat = $profilCandidat;
        return $this;
    }


    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;
        return $this;
    }

    public function isAllowed(): ?int
    {
        return $this->allowed;
    }

    public function setAllowed(int $allowed): self
    {
        $this->allowed = $allowed;

        return $this;
    }

}
