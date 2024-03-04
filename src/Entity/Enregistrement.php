<?php

namespace App\Entity;

use App\Repository\EnregistrementRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnregistrementRepository::class)]
class Enregistrement
{

    const SENS_DEPART = 1;

    const SENS_ARRIVE = 2;

    const SENS = [
        1 => 'Depart',
        2 => 'Arrivé'
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $numero;


    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'enregistrements')]
    private $dossier;


    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'smallint')]
    private $sens;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }






    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getFichier(): ?FichierAdmin
    {
        return $this->fichier;
    }

    public function setFichier(?FichierAdmin $fichier): self
    {

        $this->fichier = $fichier;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {

        return $this->date && !in_array($this->date->format('Y'), ['-0001', '0000']) ? $this->date : null;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSens(): ?int
    {
        return $this->sens;
    }

    public function setSens(int $sens): self
    {
        $this->sens = $sens;

        return $this;
    }
}
