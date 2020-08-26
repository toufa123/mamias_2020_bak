<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
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
     * @ORM\Column(type="string", length=128, nullable=true, name="country_code")
     */
    private $countryCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true, name="country")
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", nullable=true, name="latitude", scale=8)
     */
    private $latitude;

    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", nullable=true, name="longitude", scale=8)
     */
    private $longitude;

    /**
     * The Literature related to the Species and Its occurence at mediterranean national level.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Literature", mappedBy="country",cascade={"all"}, orphanRemoval=true)
     */
    private $Lit;

    public function __construct()
    {
        $this->Lit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Literature[]
     */
    public function getLit(): Collection
    {
        return $this->Lit;
    }

    public function addLit(Literature $lit): self
    {
        if (!$this->Lit->contains($lit)) {
            $this->Lit[] = $lit;
            $lit->setCountry($this);
        }

        return $this;
    }

    public function removeLit(Literature $lit): self
    {
        if ($this->Lit->contains($lit)) {
            $this->Lit->removeElement($lit);
            // set the owning side to null (unless already changed)
            if ($lit->getCountry() === $this) {
                $lit->setCountry(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getCountry();   // TODO: Implement __toString() method.
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }
}
