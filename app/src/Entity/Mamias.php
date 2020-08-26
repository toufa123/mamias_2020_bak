<?php

namespace App\Entity;

use App\Repository\MamiasRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OrderBy;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\Index;

/**
 *
 * @ORM\Entity(repositoryClass=MamiasRepository::class)
 * @UniqueEntity(fields={"relation"}, message="It looks like your already have this species in MAMIAS!")
 *
 */
class Mamias
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     *
     */
    private $id;

    /**
     * The Scientifc Name of the Species From The Catalogue.
     *
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Catalogue", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $relation;

    /**
     * Fisrt Mediterranean Sighting.
     *
     * @ORM\Column(name="first_med_sighting", type="string",  nullable=true)
     */
    private $firstMedSighting;

    /**
     * Ecofunctional Group.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Ecofunctional", fetch="EAGER")
     */
    private $Ecofunctional;

    /**
     * Origin of the Species.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Origin", fetch="EAGER")
     */
    private $Origin;

    /**
     * Status of the Species in The Mediterranean.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", fetch="EAGER")
     */
    private $speciesstatus;

    /**
     * Establishement/Sucess of the Species in The Mediterranean.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SuccessType", fetch="EAGER")
     */
    private $Success;

    /**
     * Invasiveness of the Species in The Mediterranean.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Invasiveness", fetch="EAGER")
     */
    private $invasive;

    /**
     * Establishement/Sucess of the Species in The Mediterranean.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Literature", fetch="EAGER")
     */
    private $FMedCitation;

    /**
     * Vectors and Pathways of Introduction in the Mediterranean.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Pathway", mappedBy="mamias",cascade={"all"}, orphanRemoval=true)
     */
    private $Pathway;

    /**
     * Vectors and Pathways of Introduction in the Mediterranean.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Vectors", fetch="EAGER")
     */
    private $vectors;

    /**
     * Occurence of the Species at Country Level.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CountryDistribution",indexBy="mamias",
     *     mappedBy="mamias",cascade={"all"}, orphanRemoval=true)
     * @OrderBy({"AreaSighting" = "ASC"})
     */
    private $Distribution;

    /**
     * Georeference of the occurence of the Species.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\GeoOccurence", mappedBy="mamias",cascade={"all"}, orphanRemoval=true)
     */
    private $Geo;

    /**
     * The Literature related to the Species and Its occurence at mediterranean national level.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Literature", mappedBy="mamias",cascade={"all"}, orphanRemoval=true)
     */
    private $Lit;

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
     * text|null.
     *
     * @ORM\Column(name="notes",type="text",nullable=true)
     */
    private $notes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */
    private $status;

    public function __construct()
    {
        $this->VectorName = new ArrayCollection();
        $this->Distribution = new ArrayCollection();
        $this->Geo = new ArrayCollection();
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
        $this->Lit = new ArrayCollection();
        $this->Pathway = new ArrayCollection();
        $this->vectors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstMedSighting(): ?string
    {
        return $this->firstMedSighting;
    }

    public function setFirstMedSighting(?string $firstMedSighting): self
    {
        $this->firstMedSighting = $firstMedSighting;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

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

    public function getEcofunctional(): ?Ecofunctional
    {
        return $this->Ecofunctional;
    }


    public function setEcofunctional(?Ecofunctional $Ecofunctional): self
    {
        $this->Ecofunctional = $Ecofunctional;

        return $this;
    }

    public function getOrigin(): ?Origin
    {
        return $this->Origin;
    }

    public function setOrigin(?Origin $Origin): self
    {
        $this->Origin = $Origin;

        return $this;
    }

    public function getSpeciesstatus(): ?Status
    {
        return $this->speciesstatus;
    }

    public function setSpeciesstatus(?Status $speciesstatus): self
    {
        $this->speciesstatus = $speciesstatus;

        return $this;
    }

    public function getSuccess(): ?SuccessType
    {
        return $this->Success;
    }

    public function setSuccess(?SuccessType $Success): self
    {
        $this->Success = $Success;

        return $this;
    }

    public function getInvasive(): ?Invasiveness
    {
        return $this->invasive;
    }

    public function setInvasive(?Invasiveness $invasive): self
    {
        $this->invasive = $invasive;

        return $this;
    }

    public function getFMedCitation(): ?Literature
    {
        return $this->FMedCitation;
    }

    public function setFMedCitation(?Literature $FMedCitation): self
    {
        $this->FMedCitation = $FMedCitation;

        return $this;
    }

    /**
     * @return Collection|Pathway[]
     */
    public function getPathway(): Collection
    {
        return $this->Pathway;
    }

    public function addPathway(Pathway $pathway): self
    {
        if (!$this->Pathway->contains($pathway)) {
            $this->Pathway[] = $pathway;
            $pathway->setMamias($this);
        }

        return $this;
    }

    public function removePathway(Pathway $pathway): self
    {
        if ($this->Pathway->contains($pathway)) {
            $this->Pathway->removeElement($pathway);
            // set the owning side to null (unless already changed)
            if ($pathway->getMamias() === $this) {
                $pathway->setMamias(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vectors[]
     */
    public function getVectors(): Collection
    {
        return $this->vectors;
    }

    public function addVector(Vectors $vector): self
    {
        if (!$this->vectors->contains($vector)) {
            $this->vectors[] = $vector;
        }

        return $this;
    }

    public function removeVector(Vectors $vector): self
    {
        if ($this->vectors->contains($vector)) {
            $this->vectors->removeElement($vector);
        }

        return $this;
    }

    /**
     * @return Collection|CountryDistribution[]
     */
    public function getDistribution(): Collection
    {
        return $this->Distribution;
    }

    public function addDistribution(CountryDistribution $distribution): self
    {
        if (!$this->Distribution->contains($distribution)) {
            $this->Distribution[] = $distribution;
            $distribution->setMamias($this);
        }

        return $this;
    }

    public function removeDistribution(CountryDistribution $distribution): self
    {
        if ($this->Distribution->contains($distribution)) {
            $this->Distribution->removeElement($distribution);
            // set the owning side to null (unless already changed)
            if ($distribution->getMamias() === $this) {
                $distribution->setMamias(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GeoOccurence[]
     */
    public function getGeo(): Collection
    {
        return $this->Geo;
    }

    public function addGeo(GeoOccurence $geo): self
    {
        if (!$this->Geo->contains($geo)) {
            $this->Geo[] = $geo;
            $geo->setMamias($this);
        }

        return $this;
    }

    public function removeGeo(GeoOccurence $geo): self
    {
        if ($this->Geo->contains($geo)) {
            $this->Geo->removeElement($geo);
            // set the owning side to null (unless already changed)
            if ($geo->getMamias() === $this) {
                $geo->setMamias(null);
            }
        }

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
            $lit->setMamias($this);
        }

        return $this;
    }

    public function removeLit(Literature $lit): self
    {
        if ($this->Lit->contains($lit)) {
            $this->Lit->removeElement($lit);
            // set the owning side to null (unless already changed)
            if ($lit->getMamias() === $this) {
                $lit->setMamias(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getRelation();   // TODO: Implement __toString() method.
    }

    public function getRelation(): ?Catalogue
    {
        return $this->relation;
    }

    public function setRelation(?Catalogue $relation): self
    {
        $this->relation = $relation;

        return $this;
    }
}
