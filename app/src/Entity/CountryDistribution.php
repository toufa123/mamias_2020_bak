<?php

namespace App\Entity;

use App\Repository\CountryDistributionRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass=CountryDistributionRepository::class)
 */
class CountryDistribution
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="area_sighting", type="string",  nullable=true)
     */
    private $AreaSighting;

    /**
     * @ORM\Column(name="fisrt_sighting", type="boolean",  nullable=true)
     */
    private $fisrtSighting;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", fetch="EAGER")
     */
    private $nationalstatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SuccessType", fetch="EAGER")
     */
    private $areaSuccess;

    /**
     * @var string
     *
     * @ORM\Column(name="area_notes", type="text", nullable=true)
     */
    private $areaNote;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime",options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at",type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", fetch="EAGER")
     */
    private $country;
    /**
     * @var \EcAp
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EcAp", fetch="EAGER")
     */
    private $ecap;

    /**
     * @var \RegionalSea
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RegionalSea", fetch="EAGER")
     */
    private $regionalSea;

    /**
     * @var \VectorName
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\VectorName", fetch="EAGER")
     */
    private $VectorName;

    /**
     * @var \Certainty
     * @ORM\Column(name="Certainty", type="text", nullable=true)
     *
     */
    private $Certainty;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mamias", inversedBy="Distribution", fetch="EAGER")
     */
    private $mamias;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
        //$this->firstAreaSighting = new\DateTime('now');
        $this->vector = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaSighting(): ?string
    {
        return $this->AreaSighting;
    }

    public function setAreaSighting(?string $AreaSighting): self
    {
        $this->AreaSighting = $AreaSighting;

        return $this;
    }

    public function getFisrtSighting(): ?bool
    {
        return $this->fisrtSighting;
    }

    public function setFisrtSighting(?bool $fisrtSighting): self
    {
        $this->fisrtSighting = $fisrtSighting;

        return $this;
    }

    public function getAreaNote(): ?string
    {
        return $this->areaNote;
    }

    public function setAreaNote(?string $areaNote): self
    {
        $this->areaNote = $areaNote;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCertainty(): ?string
    {
        return $this->Certainty;
    }

    public function setCertainty(?string $Certainty): self
    {
        $this->Certainty = $Certainty;

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

    public function getNationalstatus(): ?Status
    {
        return $this->nationalstatus;
    }

    public function setNationalstatus(?Status $nationalstatus): self
    {
        $this->nationalstatus = $nationalstatus;

        return $this;
    }

    public function getAreaSuccess(): ?SuccessType
    {
        return $this->areaSuccess;
    }

    public function setAreaSuccess(?SuccessType $areaSuccess): self
    {
        $this->areaSuccess = $areaSuccess;

        return $this;
    }

    public function getEcap(): ?EcAp
    {
        return $this->ecap;
    }

    public function setEcap(?EcAp $ecap): self
    {
        $this->ecap = $ecap;

        return $this;
    }

    public function getRegionalSea(): ?RegionalSea
    {
        return $this->regionalSea;
    }

    public function setRegionalSea(?RegionalSea $regionalSea): self
    {
        $this->regionalSea = $regionalSea;

        return $this;
    }

    public function getVectorName(): ?VectorName
    {
        return $this->VectorName;
    }

    public function setVectorName(?VectorName $VectorName): self
    {
        $this->VectorName = $VectorName;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getMamias() . '/' . $this->getCountry();   // TODO: Implement __toString() method.
    }

    public function getMamias(): ?Mamias
    {
        return $this->mamias;
    }

    public function setMamias(?Mamias $mamias): self
    {
        $this->mamias = $mamias;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
