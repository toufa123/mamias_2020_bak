<?php

namespace App\Entity;

use App\Repository\RegionalSeaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegionalSeaRepository::class)
 */
class RegionalSea
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
     * @ORM\Column(type="string", length=128, nullable=true, name="regional_sea_code")
     */
    private $regionalSeaCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true, name="regional_sea")
     */
    private $regionalSea;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegionalSeaCode(): ?string
    {
        return $this->regionalSeaCode;
    }

    public function setRegionalSeaCode(?string $regionalSeaCode): self
    {
        $this->regionalSeaCode = $regionalSeaCode;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getRegionalSea();        // TODO: Implement __toString() method.
    }

    public function getRegionalSea(): ?string
    {
        return $this->regionalSea;
    }

    public function setRegionalSea(?string $regionalSea): self
    {
        $this->regionalSea = $regionalSea;

        return $this;
    }
}
