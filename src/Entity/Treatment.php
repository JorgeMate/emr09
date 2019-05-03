<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreatmentRepository")
 */
class Treatment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Type", inversedBy="treatments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Opera", mappedBy="treatment", orphanRemoval=true)
     */
    private $operas;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     */
    private $enabled;



    

    public function __construct()
    {
        $this->operas = new ArrayCollection();
        
    }

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection|Opera[]
     */
    public function getOperas(): Collection
    {
        return $this->operas;
    }

    public function addOpera(Opera $opera): self
    {
        if (!$this->operas->contains($opera)) {
            $this->operas[] = $opera;
            $opera->setTreatment($this);
        }

        return $this;
    }

    public function removeOpera(Opera $opera): self
    {
        if ($this->operas->contains($opera)) {
            $this->operas->removeElement($opera);
            // set the owning side to null (unless already changed)
            if ($opera->getTreatment() === $this) {
                $opera->setTreatment(null);
            }
        }

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

}
