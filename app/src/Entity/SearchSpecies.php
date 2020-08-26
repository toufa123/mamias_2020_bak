<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#use Doctrine\ORM\Mapping as ORM;

class SearchSpecies
{
    /**
     * @var string | null
     */
    private $speciesName;

    /**
     * @var int | null
     */
    private $med1stSighting;

    /**
     * @var string| null
     */
    private $ecofunctional;

    /**
     * @var string| null
     */
    private $origin;

    /**
     * @var string| null
     */
    private $successType;

    /**
     * @var string| null
     */
    private $country;

    /**
     * @var string| null
     */
    private $regionalSea;

    /**
     * @var string| null
     */
    private $invasive;

    /**
     * @var string| null
     */
    private $Ecap;

    /**
     * @var string| null
     */
    private $status;

    /**
     * @var string| null
     */
    private $vectorName;


    public function getInvasive(): ?string
    {
        return $this->invasive;
    }

    public function setInvasive(?string $invasive): self
    {
        $this->invasive = $invasive;

        return $this;
    }

    public function getSpeciesName(): ?string
    {
        return $this->speciesName;
    }

    public function setSpeciesName(?string $speciesName): self
    {
        $this->speciesName = $speciesName;

        return $this;
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

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
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

    public function getEcap(): ?string
    {
        return $this->Ecap;
    }

    public function setEcap(?string $Ecap): self
    {
        $this->Ecap = $Ecap;

        return $this;
    }

    public function getMed1stSighting(): ?int
    {
        return $this->med1stSighting;
    }

    public function setMed1stSighting(?int $med1stSighting): self
    {
        $this->med1stSighting = $med1stSighting;

        return $this;
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


    public function getvectorName(): ?string
    {
        return $this->vectorName;
    }

    public function setvectorName(?string $vectorName): self
    {
        $this->vectorName = $vectorName;

        return $this;
    }
}
