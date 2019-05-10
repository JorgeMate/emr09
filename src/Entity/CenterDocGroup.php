<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CenterDocGroupRepository")
 */
class CenterDocGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Center", inversedBy="centerDocGroups")
     */
    private $center;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserDoc", mappedBy="centerDocGroup")
     */
    private $userDocs;

    public function __construct()
    {
        $this->userDocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCenter(): ?Center
    {
        return $this->center;
    }

    public function setCenter(?Center $center): self
    {
        $this->center = $center;

        return $this;
    }

    /**
     * @return Collection|UserDoc[]
     */
    public function getUserDocs(): Collection
    {
        return $this->userDocs;
    }

    public function addUserDoc(UserDoc $userDoc): self
    {
        if (!$this->userDocs->contains($userDoc)) {
            $this->userDocs[] = $userDoc;
            $userDoc->setCenterdocgroup($this);
        }

        return $this;
    }

    public function removeUserDoc(UserDoc $userDoc): self
    {
        if ($this->userDocs->contains($userDoc)) {
            $this->userDocs->removeElement($userDoc);
            // set the owning side to null (unless already changed)
            if ($userDoc->getCenterdocgroup() === $this) {
                $userDoc->setCenterdocgroup(null);
            }
        }

        return $this;
    }
}
