<?php

namespace App\Entity;

use App\Repository\InvasivenessRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvasivenessRepository::class)
 */
class Invasiveness
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invasivness;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $def;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDef(): ?string
    {
        return $this->def;
    }

    public function setDef(?string $def): self
    {
        $this->def = $def;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getInvasivness(); // TODO: Implement __toString() method.
    }

    public function getInvasivness(): ?string
    {
        return $this->invasivness;
    }

    public function setInvasivness(?string $invasivness): self
    {
        $this->invasivness = $invasivness;

        return $this;
    }
}
