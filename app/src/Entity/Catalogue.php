<?php

namespace App\Entity;

use App\Repository\CatalogueRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Table(name="catalogue")
 * @UniqueEntity(fields={"Species"}, message="It looks like your already have this species in the Catlogue!")
 * @ORM\Table(indexes={@Index(name="species_idx", columns={"Species"})})
 * @ORM\Entity(repositoryClass=CatalogueRepository::class)
 */
class Catalogue
{
    /**
     * One Species has many Synonyms (WoRMS).
     *
     * @ORM\OneToMany(targetEntity="Synonym", mappedBy="Catalogue", cascade={"ALL"}, orphanRemoval=true)
     */
    protected $Synonyms;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @var string|null The Scientific name of the Species verified through WoRMS webservice
     *
     * @ORM\Column(name="Species", type="string", length=255, nullable=false, unique=true)
     */
    private $Species;
    /**
     * @var string|null A specis Code composer of 6 caracteres
     *
     * @ORM\Column(name="species_code", type="string", length=128, nullable=true)
     */
    private $speciesCode;
    /**
     * @var string|null The Authority follwing WorMS
     *
     * @ORM\Column(name="authority", type="string", length=100, nullable=true)
     */
    private $authority;
    /**
     * @var string|null The Aphia Code (WoRMS)
     *
     * @ORM\Column(name="Aphia", type="integer", length=10, nullable=true)
     */
    private $Aphia;
    /**
     * @var string|null The WorMS link
     *
     * @ORM\Column(name="WormsUrl", type="string", length=255, nullable=true)
     */
    private $WormsUrl;
    /**
     * @var string|null Taxonomy : Kingdom
     *
     * @ORM\Column(name="kingdom", type="string", length=100, nullable=true)
     */
    private $kingdom;
    /**
     * @var string|null Taxonomy : phylum
     *
     * @ORM\Column(name="phylum", type="string", length=100, nullable=true)
     */
    private $phylum;
    /**
     * @var string|null Taxonomy : class
     *
     * @ORM\Column(name="class", type="string", length=100, nullable=true)
     */
    private $class;
    /**
     * @var string|null Taxonomy : Order
     *
     * @ORM\Column(name="ordersp", type="string", length=100, nullable=true)
     */
    private $ordersp;
    /**
     * @var string|null Taxonomy : Family
     *
     * @ORM\Column(name="family", type="string", length=100, nullable=true)
     */
    private $family;
    /**
     * @var string|null Taxonomic Referenciel : phylum
     *
     * @ORM\Column(name="refTax", type="string", length=128, nullable=true)
     */
    private $refTax;
    /**
     * @var string|null ITIS link
     *
     * @ORM\Column(name="itislink", type="string", nullable=true)
     */
    private $itisLink;
    /**
     * @var string|null CoL Link
     *
     * @ORM\Column(name="CoLlink", type="string",  nullable=true)
     */
    private $CoLlink;
    /**
     * @var string|null EoL Link
     *
     * @ORM\Column(name="EoLlink", type="string", nullable=true)
     */
    private $EoLlink;
    /**
     * @var text|null GBIF Link
     *
     * @ORM\Column(name="GBIFlink", type="text", nullable=true)
     */
    private $GBIFlink;
    /**
     * @var string|null Status of the Species in the Catalogue
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */
    private $status;
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
     * @var text|null Notes
     * @ORM\Column(name="catalogue_notes",type="text",nullable=true)
     */
    private $catalogue_notes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
        $this->Synonyms = new ArrayCollection();
        $this->status = 'Validated';
        $this->Synonym = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthority(): ?string
    {
        return $this->authority;
    }

    public function setAuthority(?string $authority): self
    {
        $this->authority = $authority;

        return $this;
    }

    public function getAphia(): ?int
    {
        return $this->Aphia;
    }

    public function setAphia(?int $Aphia): self
    {
        $this->Aphia = $Aphia;

        return $this;
    }

    public function getWormsUrl(): ?string
    {
        return $this->WormsUrl;
    }

    public function setWormsUrl(?string $WormsUrl): self
    {
        $this->WormsUrl = $WormsUrl;

        return $this;
    }

    public function getKingdom(): ?string
    {
        return $this->kingdom;
    }

    public function setKingdom(?string $kingdom): self
    {
        $this->kingdom = $kingdom;

        return $this;
    }

    public function getPhylum(): ?string
    {
        return $this->phylum;
    }

    public function setPhylum(?string $phylum): self
    {
        $this->phylum = $phylum;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getOrdersp(): ?string
    {
        return $this->ordersp;
    }

    public function setOrdersp(?string $ordersp): self
    {
        $this->ordersp = $ordersp;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getRefTax(): ?string
    {
        return $this->refTax;
    }

    public function setRefTax(?string $refTax): self
    {
        $this->refTax = $refTax;

        return $this;
    }

    public function getItisLink(): ?string
    {
        return $this->itisLink;
    }

    public function setItisLink(?string $itisLink): self
    {
        $this->itisLink = $itisLink;

        return $this;
    }

    public function getCoLlink(): ?string
    {
        return $this->CoLlink;
    }

    public function setCoLlink(?string $CoLlink): self
    {
        $this->CoLlink = $CoLlink;

        return $this;
    }

    public function getEoLlink(): ?string
    {
        return $this->EoLlink;
    }

    public function setEoLlink(?string $EoLlink): self
    {
        $this->EoLlink = $EoLlink;

        return $this;
    }

    public function getGBIFlink(): ?string
    {
        return $this->GBIFlink;
    }

    public function setGBIFlink(?string $GBIFlink): self
    {
        $this->GBIFlink = $GBIFlink;

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

    public function getCatalogueNotes(): ?string
    {
        return $this->catalogue_notes;
    }

    public function setCatalogueNotes(?string $catalogue_notes): self
    {
        $this->catalogue_notes = $catalogue_notes;

        return $this;
    }

    /**
     * @return Collection|Synonym[]
     */
    public function getSynonyms(): Collection
    {
        return $this->Synonyms;
    }

    public function addSynonym(Synonym $synonym): self
    {
        if (!$this->Synonyms->contains($synonym)) {
            $this->Synonyms[] = $synonym;
            $synonym->setCatalogue($this);
        }

        return $this;
    }

    public function removeSynonym(Synonym $synonym): self
    {
        if ($this->Synonyms->contains($synonym)) {
            $this->Synonyms->removeElement($synonym);
            // set the owning side to null (unless already changed)
            if ($synonym->getCatalogue() === $this) {
                $synonym->setCatalogue(null);
            }
        }

        return $this;
    }

    public function getSpeciesCode(): ?string
    {
        return $this->speciesCode;
    }

    public function setSpeciesCode(?string $speciesCode): self
    {
        $this->speciesCode = $speciesCode;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getSpecies();   // TODO: Implement __toString() method.
    }

    public function getSpecies(): ?string
    {
        return $this->Species;
    }

    public function setSpecies(string $Species): self
    {
        $this->Species = $Species;

        return $this;
    }


}
