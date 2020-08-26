<?php

namespace App\Entity;

use App\Repository\VectorNameRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="vector_name")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 *
 */
class VectorName
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $vectorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vectorIcone;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer", nullable=true, name="lft")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer", nullable=true, name="lvl")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer", nullable=true, name="rgt")
     */
    private $rgt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\VectorName")
     * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="App\Entity\VectorName", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\VectorName", mappedBy="parent")
     * @ORM\OrderBy({"lft":"ASC"})
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVectorIcone(): ?string
    {
        return $this->vectorIcone;
    }

    public function setVectorIcone(?string $vectorIcone): self
    {
        $this->vectorIcone = $vectorIcone;

        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(?int $lft): self
    {
        $this->lft = $lft;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(?int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(?int $rgt): self
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getRoot(): ?self
    {
        return $this->root;
    }

    public function setRoot(?self $root): self
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return Collection|VectorName[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(VectorName $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(VectorName $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getVectorName();   // TODO: Implement __toString() method.
    }

    public function getVectorName(): ?string
    {
        return $this->vectorName;
    }

    public function setVectorName(?string $vectorName): self
    {
        $this->vectorName = $vectorName;

        return $this;
    }

}
