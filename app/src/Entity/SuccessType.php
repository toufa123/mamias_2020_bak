<?php

namespace App\Entity;

use App\Repository\SuccessTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuccessTypeRepository::class)
 */
class SuccessType
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
     * @ORM\Column(type="string", length=32, nullable=true, name="success_code")
     */
    private $successCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true, name="success_type")
     */
    private $successType;

    /**
     * @var text|null
     *
     * @ORM\Column(type="text", name="success_exp", nullable=true)
     */
    private $successexpl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuccessCode(): ?string
    {
        return $this->successCode;
    }

    public function setSuccessCode(?string $successCode): self
    {
        $this->successCode = $successCode;

        return $this;
    }

    public function getSuccessexpl(): ?string
    {
        return $this->successexpl;
    }

    public function setSuccessexpl(?string $successexpl): self
    {
        $this->successexpl = $successexpl;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getSuccessType();   // TODO: Implement __toString() method.
    }

    public function getSuccessType(): ?string
    {
        return $this->successType;
    }

    public function setSuccessType(?string $successType): self
    {
        $this->successType = $successType;

        return $this;
    }
}
