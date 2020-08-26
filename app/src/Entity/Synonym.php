<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SynonymRepository")
 *
 */
class Synonym
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Species_Synonym", type="string", length=255, nullable=true)
     */
    private $Species_Synonym;

    /**
     * Many synonyms  have one species of the catalogue. This is the owning side.
     *
     * @ORM\ManyToOne(targetEntity="Catalogue", inversedBy="Synonyms", fetch="EAGER")
     */
    private $Catalogue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCatalogue(): ?Catalogue
    {
        return $this->Catalogue;
    }

    public function setCatalogue(?Catalogue $Catalogue): self
    {
        $this->Catalogue = $Catalogue;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getSpeciesSynonym();   // TODO: Implement __toString() method.
    }

    public function getSpeciesSynonym(): ?string
    {
        return $this->Species_Synonym;
    }

    public function setSpeciesSynonym(?string $Species_Synonym): self
    {
        $this->Species_Synonym = $Species_Synonym;

        return $this;
    }
}
