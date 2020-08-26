<?php

namespace App\Entity;

class SearchCountry
{
    /**
     * @var string| null
     */
    private $country;

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
