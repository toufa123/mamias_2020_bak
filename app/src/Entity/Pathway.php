<?php

namespace App\Entity;

use App\Repository\PathwayRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PathwayRepository::class)
 */
class Pathway
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Mamias", inversedBy="Pathway",fetch="EAGER")
     */
    private $mamias;

    /**
     * Vectors and Pathways of Introduction in the Mediterranean.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\VectorName", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $VectorName;

    /**
     * @var string|null
     * @ORM\Column(name="Certainty", type="string", nullable=true)
     */
    private $Certainty;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCertainty(): ?string
    {
        return $this->Certainty;
    }

    public function setCertainty(?string $Certainty): self
    {
        $this->Certainty = $Certainty;

        return $this;
    }

    public function getMamias(): ?Mamias
    {
        return $this->mamias;
    }

    public function setMamias(?Mamias $mamias): self
    {
        $this->mamias = $mamias;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getVectorName();    // TODO: Implement __toString() method.
    }

    public function getVectorName(): ?VectorName
    {
        return $this->VectorName;
    }

    public function setVectorName(?VectorName $VectorName): self
    {
        $this->VectorName = $VectorName;

        return $this;
    }

}
