<?php

namespace App\Entity;

use App\Repository\VectorsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VectorsRepository::class)
 */
class Vectors
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $vectorName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getVectorName();   // TODO: Implement __toString() method.
    }

    public function getVectorName(): ?string
    {
        return $this->vectorName;
    }

    public function setVectorName(?string $vectorName): self
    {
        $this->vectorName = $vectorName;

        return $this;
    }
}
