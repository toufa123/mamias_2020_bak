<?php

namespace App\Entity;

use App\Repository\OriginRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OriginRepository::class)
 */
class Origin
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
     * @ORM\Column(type="string", length=128, nullable=true, name="origin_code")
     */
    private $originCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true, name="origin_region")
     */
    private $originRegion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginCode(): ?string
    {
        return $this->originCode;
    }

    public function setOriginCode(?string $originCode): self
    {
        $this->originCode = $originCode;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getOriginRegion();   // TODO: Implement __toString() method.
    }

    public function getOriginRegion(): ?string
    {
        return $this->originRegion;
    }

    public function setOriginRegion(?string $originRegion): self
    {
        $this->originRegion = $originRegion;

        return $this;
    }
}
