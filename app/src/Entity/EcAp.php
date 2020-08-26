<?php

namespace App\Entity;

use App\Repository\EcApRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ecap")
 * @ORM\Entity(repositoryClass=EcApRepository::class)
 */
class EcAp
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
     * @ORM\Column(type="string", length=16, nullable=true, name="code")
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true, name="ecap")
     */
    private $ecap;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getEcap();   // TODO: Implement __toString() method.
    }

    public function getEcap(): ?string
    {
        return $this->ecap;
    }

    public function setEcap(?string $ecap): self
    {
        $this->ecap = $ecap;

        return $this;
    }
}
