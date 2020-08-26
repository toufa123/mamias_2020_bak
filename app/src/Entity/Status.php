<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class Status
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
     * @ORM\Column(type="string", length=128, nullable=true, name="status")
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true, name="def")
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
        return (string)$this->getStatus();   // TODO: Implement __toString() method.
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
