<?php

namespace App\Entity;

use App\Repository\EcofunctionalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EcofunctionalRepository::class)
 */
class Ecofunctional
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
     * @ORM\Column(type="string", length=128, nullable=true, name="ecofunctional_code")
     */
    private $ecofunctionalCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true, name="ecofunctional")
     */
    private $ecofunctional;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEcofunctionalCode(): ?string
    {
        return $this->ecofunctionalCode;
    }

    public function setEcofunctionalCode(?string $ecofunctionalCode): self
    {
        $this->ecofunctionalCode = $ecofunctionalCode;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getEcofunctional();   // TODO: Implement __toString() method.
    }

    public function getEcofunctional(): ?string
    {
        return $this->ecofunctional;
    }

    public function setEcofunctional(?string $ecofunctional): self
    {
        $this->ecofunctional = $ecofunctional;

        return $this;
    }
}
