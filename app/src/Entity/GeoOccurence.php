<?php

namespace App\Entity;

use App\Application\Sonata\UserBundle\Entity\User;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Date;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass="App\Repository\GeoOccurenceRepository")
 * @Vich\Uploadable
 *
 */
class GeoOccurence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $date_occurence;
    /**
     * @var CrEOF\Spatial\PHP\Types\Geometry\Point
     * @ORM\Column(name="location", type="point", nullable=true)
     */
    private $Location;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note_occurence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", fetch="EAGER")
     */
    private $country;

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
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="depth", type="integer", nullable=true)
     */
    private $depth;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plants_animals", type="text", nullable=true)
     */
    private $PlantsAnimals;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estimated_measured", type="text", nullable=true)
     */
    private $EstimatedMeasured;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nvalues", type="integer", nullable=true)
     */
    private $nvalues;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User", fetch="EAGER")
     */
    private $user;

    /**
     *
     * @ORM\ManyToMany(targetEntity="App\Application\Sonata\UserBundle\Entity\User", fetch="EAGER")
     */
    private $validator;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="GeoOccurence_image", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mamias", inversedBy="Geo")
     */
    private $mamias;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->updatedAt = new DateTime('now');
        //$this->date_occurence = new \Date('now');

        //$this->user = $this->setUser('user');
        //$this->firstAreaSighting = new\DateTime('now');
        $this->validator = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoteOccurence(): ?string
    {
        return $this->note_occurence;
    }

    public function setNoteOccurence(?string $note_occurence): self
    {
        $this->note_occurence = $note_occurence;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

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

    public function __toString()
    {
        return (string)$this->getMamias()->getRelation();   // TODO: Implement __toString() method.
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getLocation()
    {
        return $this->Location;
    }

    public function setLocation($Location): self
    {
        $this->Location = $Location;

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

    public function getDateOccurence(): ?\DateTimeInterface
    {
        return $this->date_occurence;
    }

    public function setDateOccurence(?\DateTimeInterface $date_occurence): self
    {
        $this->date_occurence = $date_occurence;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getValidator(): Collection
    {
        return $this->validator;
    }

    public function addValidator(User $validator): self
    {
        if (!$this->validator->contains($validator)) {
            $this->validator[] = $validator;
        }

        return $this;
    }

    public function removeValidator(User $validator): self
    {
        if ($this->validator->contains($validator)) {
            $this->validator->removeElement($validator);
        }

        return $this;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function setDepth(?int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    public function getNvalues(): ?int
    {
        return $this->nvalues;
    }

    public function setNvalues(?int $nvalues): self
    {
        $this->nvalues = $nvalues;

        return $this;
    }

    public function getPlantsAnimals(): ?string
    {
        return $this->PlantsAnimals;
    }

    public function setPlantsAnimals(?string $PlantsAnimals): self
    {
        $this->PlantsAnimals = $PlantsAnimals;

        return $this;
    }

    public function getEstimatedMeasured(): ?string
    {
        return $this->EstimatedMeasured;
    }

    public function setEstimatedMeasured(?string $EstimatedMeasured): self
    {
        $this->EstimatedMeasured = $EstimatedMeasured;

        return $this;
    }


}
