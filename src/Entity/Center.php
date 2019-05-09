<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CenterRepository")
 */
class Center
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contact_person;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="center")
     */
    private $users;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Insurance", mappedBy="center")
     */
    private $insurances;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"}) 
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Source", mappedBy="center")
     */
    private $sources;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="center")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Type", mappedBy="center", orphanRemoval=true)
     */
    private $types;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Place", mappedBy="center", orphanRemoval=true)
     */
    private $places;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CenterDocGroup", mappedBy="center")
     */
    private $centerDocGroups;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->insurances = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->centerDocGroups = new ArrayCollection();
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

    public function getContactPerson(): ?string
    {
        return $this->contact_person;
    }

    public function setContactPerson(?string $contact_person): self
    {
        $this->contact_person = $contact_person;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getPatientsNo() //: Collection
    {
        $patientsNo = 0;
        foreach ($this->users as $user) {
            $patientsNo += $user->getPatients()->count();
        }
        return $patientsNo;
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

    /**
     * @return Collection|Insurance[]
     */
    public function getInsurances(): Collection
    {
        return $this->insurances;
    }

    public function addInsurance(Insurance $insurance): self
    {
        if (!$this->insurances->contains($insurance)) {
            $this->insurances[] = $insurance;
            $insurance->setCenter($this);
        }

        return $this;
    }

    public function removeInsurance(Insurance $insurance): self
    {
        if ($this->insurances->contains($insurance)) {
            $this->insurances->removeElement($insurance);
            // set the owning side to null (unless already changed)
            if ($insurance->getCenter() === $this) {
                $insurance->setCenter(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
            $source->setCenter($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            // set the owning side to null (unless already changed)
            if ($source->getCenter() === $this) {
                $source->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setCenter($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getCenter() === $this) {
                $tag->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Type[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->setCenter($this);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->contains($type)) {
            $this->types->removeElement($type);
            // set the owning side to null (unless already changed)
            if ($type->getCenter() === $this) {
                $type->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setCenter($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->contains($place)) {
            $this->places->removeElement($place);
            // set the owning side to null (unless already changed)
            if ($place->getCenter() === $this) {
                $place->setCenter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CenterDocGroup[]
     */
    public function getCenterDocGroups(): Collection
    {
        return $this->centerDocGroups;
    }

    public function addCenterDocGroup(CenterDocGroup $centerDocGroup): self
    {
        if (!$this->centerDocGroups->contains($centerDocGroup)) {
            $this->centerDocGroups[] = $centerDocGroup;
            $centerDocGroup->setCenter($this);
        }

        return $this;
    }

    public function removeCenterDocGroup(CenterDocGroup $centerDocGroup): self
    {
        if ($this->centerDocGroups->contains($centerDocGroup)) {
            $this->centerDocGroups->removeElement($centerDocGroup);
            // set the owning side to null (unless already changed)
            if ($centerDocGroup->getCenter() === $this) {
                $centerDocGroup->setCenter(null);
            }
        }

        return $this;
    }
}
