<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MedicatRepository")
 */
class Medicat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="medicats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="medicats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $medicat;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $dosis;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stop_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getMedicat(): ?string
    {
        return $this->medicat;
    }

    public function setMedicat(string $medicat): self
    {
        $this->medicat = $medicat;

        return $this;
    }

    public function getDosis(): ?string
    {
        return $this->dosis;
    }

    public function setDosis(?string $dosis): self
    {
        $this->dosis = $dosis;

        return $this;
    }

    public function getStopAt(): ?\DateTimeInterface
    {
        return $this->stop_at;
    }

    public function setStopAt(?\DateTimeInterface $stop_at): self
    {
        $this->stop_at = $stop_at;

        return $this;
    }
}
