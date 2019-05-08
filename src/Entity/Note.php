<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Opera", mappedBy="note")
     */
    private $operas;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paid_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function __construct()
    {
        $this->operas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $opera->setNote($this);
        }

        return $this;
    }

    public function removeOpera(Opera $opera): self
    {
        if ($this->operas->contains($opera)) {
            $this->operas->removeElement($opera);
            // set the owning side to null (unless already changed)
            if ($opera->getNote() === $this) {
                $opera->setNote(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeInterface $paid_at): self
    {
        $this->paid_at = $paid_at;

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

    
}
