<?php

namespace App\Entity;

use App\Application\Sonata\UserBundle\Entity\User;
use App\Repository\LiteratureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LiteratureRepository::class)
 */
class Literature
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
     * @ORM\Column(name="literature_code", type="string",  nullable=true)
     */
    private $literatureCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Authors", type="string",  nullable=true)
     */
    private $Authors;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Year", type="string",  nullable=true)
     */
    private $Year;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DOI", type="string", nullable=true)
     */
    private $DOI;

    /**
     * @var text|null
     *
     * @ORM\Column(name="literature", type="text", nullable=true)
     */
    private $Title;

    /**
     * @var text|null
     *
     * @ORM\Column(name="where", type="text", nullable=true)
     */
    private $where;

    /**
     * @var text|null
     *
     * @ORM\Column(name="litlink", type="text", nullable=true)
     */
    private $litlink;

    /**
     * @var text|null
     *
     * @ORM\Column(name="lit_note", type="text", nullable=true)
     */
    private $lit_note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mamias", inversedBy="Lit", fetch="EAGER")
     */
    private $mamias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="Lit", fetch="EAGER")
     */
    private $country;


    /**
     * @ORM\ManyToOne(targetEntity="App\Application\Sonata\UserBundle\Entity\User", inversedBy="literature", fetch="EAGER")
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLiteratureCode(): ?string
    {
        return $this->literatureCode;
    }

    public function setLiteratureCode(?string $literatureCode): self
    {
        $this->literatureCode = $literatureCode;

        return $this;
    }

    public function getDOI(): ?string
    {
        return $this->DOI;
    }

    public function setDOI(?string $DOI): self
    {
        $this->DOI = $DOI;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(?string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getWhere(): ?string
    {
        return $this->where;
    }

    public function setWhere(?string $where): self
    {
        $this->where = $where;

        return $this;
    }

    public function getLitlink(): ?string
    {
        return $this->litlink;
    }

    public function setLitlink(?string $litlink): self
    {
        $this->litlink = $litlink;

        return $this;
    }

    public function getLitNote(): ?string
    {
        return $this->lit_note;
    }

    public function setLitNote(?string $lit_note): self
    {
        $this->lit_note = $lit_note;

        return $this;
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

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getAuthors() . ',' . $this->getYear() . ',' . $this->getCountry();   // TODO: Implement __toString() method.
    }

    public function getAuthors(): ?string
    {
        return $this->Authors;
    }

    public function setAuthors(?string $Authors): self
    {
        $this->Authors = $Authors;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->Year;
    }

    public function setYear(?string $Year): self
    {
        $this->Year = $Year;

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
