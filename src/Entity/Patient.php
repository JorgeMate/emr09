<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient
{

    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const NUM_ITEMS = 100;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=127)
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sex;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="patients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $house_no;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $house_txt;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $cel;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $tel_con;

    /**
     * @ORM\Column(type="string", length=127, nullable=true)
     */
    private $doctor;

    /**
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    private $tel_doc;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Insurance", inversedBy="patients")
     */
    private $insurance;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="patients")
     */
    private $source;

    /**
     * @var Tag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(name="patient_tag")
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="4", maxMessage="post.too_many_tags")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consult", mappedBy="patient", orphanRemoval=true)
     */
    private $consults;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historia", mappedBy="patient", orphanRemoval=true)
     */
    private $historias;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Medicat", mappedBy="patient", orphanRemoval=true)
     */
    private $medicats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Opera", mappedBy="patient", orphanRemoval=true)
     */
    private $operas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StoredImg", mappedBy="patient", orphanRemoval=true)
     */
    private $storedImgs;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private $code1;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private $code2;


    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->consults = new ArrayCollection();
        $this->historias = new ArrayCollection();
        $this->medicats = new ArrayCollection();
        $this->operas = new ArrayCollection();
        $this->storedImgs = new ArrayCollection();

    }







    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSex(): ?bool
    {
        return $this->sex;
    }

    public function setSex(?bool $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    
    public function getAge()
    {
        $now = new \DateTime('now');
        $birth = $this->getBirthdate();
        $difference = $now->diff($birth);
        return $difference->format('%y');
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getHouseNo(): ?int
    {
        return $this->house_no;
    }

    public function setHouseNo(?int $house_no): self
    {
        $this->house_no = $house_no;

        return $this;
    }

    public function getHouseTxt(): ?string
    {
        return $this->house_txt;
    }

    public function setHouseTxt(?string $house_txt): self
    {
        $this->house_txt = $house_txt;

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

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

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

    public function getCel(): ?string
    {
        return $this->cel;
    }

    public function setCel(?string $cel): self
    {
        $this->cel = $cel;

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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getTelCon(): ?string
    {
        return $this->tel_con;
    }

    public function setTelCon(?string $tel_con): self
    {
        $this->tel_con = $tel_con;

        return $this;
    }

    public function getDoctor(): ?string
    {
        return $this->doctor;
    }

    public function setDoctor(?string $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
    }

    public function getTelDoc(): ?string
    {
        return $this->tel_doc;
    }

    public function setTelDoc(?string $tel_doc): self
    {
        $this->tel_doc = $tel_doc;

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

    public function getInsurance(): ?Insurance
    {
        return $this->insurance;
    }

    public function setInsurance(?Insurance $insurance): self
    {
        $this->insurance = $insurance;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function addTag(Tag ...$tags): void
    {
        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->tags->add($tag);
            }
        }
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @return Collection|Consult[]
     */
    public function getConsults(): Collection
    {
        return $this->consults;
    }

    public function addConsult(Consult $consult): self
    {
        if (!$this->consults->contains($consult)) {
            $this->consults[] = $consult;
            $consult->setPatient($this);
        }

        return $this;
    }

    public function removeConsult(Consult $consult): self
    {
        if ($this->consults->contains($consult)) {
            $this->consults->removeElement($consult);
            // set the owning side to null (unless already changed)
            if ($consult->getPatient() === $this) {
                $consult->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Historia[]
     */
    public function getHistorias(): Collection
    {
        return $this->historias;
    }

    public function addHistoria(Historia $historia): self
    {
        if (!$this->historias->contains($historia)) {
            $this->historias[] = $historia;
            $historia->setPatient($this);
        }

        return $this;
    }

    public function removeHistoria(Historia $historia): self
    {
        if ($this->historias->contains($historia)) {
            $this->historias->removeElement($historia);
            // set the owning side to null (unless already changed)
            if ($historia->getPatient() === $this) {
                $historia->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Medicat[]
     */
    public function getMedicats(): Collection
    {
        return $this->medicats;
    }

    public function addMedicat(Medicat $medicat): self
    {
        if (!$this->medicats->contains($medicat)) {
            $this->medicats[] = $medicat;
            $medicat->setPatient($this);
        }

        return $this;
    }

    public function removeMedicat(Medicat $medicat): self
    {
        if ($this->medicats->contains($medicat)) {
            $this->medicats->removeElement($medicat);
            // set the owning side to null (unless already changed)
            if ($medicat->getPatient() === $this) {
                $medicat->setPatient(null);
            }
        }

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
            $opera->setPatient($this);
        }

        return $this;
    }

    public function removeOpera(Opera $opera): self
    {
        if ($this->operas->contains($opera)) {
            $this->operas->removeElement($opera);
            // set the owning side to null (unless already changed)
            if ($opera->getPatient() === $this) {
                $opera->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StoredImg[]
     */
    public function getStoredImgs(): Collection
    {
        return $this->storedImgs;
    }

    public function addStoredImg(StoredImg $storedImg): self
    {
        if (!$this->storedImgs->contains($storedImg)) {
            $this->storedImgs[] = $storedImg;
            $storedImg->setPatient($this);
        }

        return $this;
    }

    public function removeStoredImg(StoredImg $storedImg): self
    {
        if ($this->storedImgs->contains($storedImg)) {
            $this->storedImgs->removeElement($storedImg);
            // set the owning side to null (unless already changed)
            if ($storedImg->getPatient() === $this) {
                $storedImg->setPatient(null);
            }
        }

        return $this;
    }

    public function getCode1(): ?string
    {
        return $this->code1;
    }

    public function setCode1(?string $code1): self
    {
        $this->code1 = $code1;

        return $this;
    }

    public function getCode2(): ?string
    {
        return $this->code2;
    }

    public function setCode2(?string $code2): self
    {
        $this->code2 = $code2;

        return $this;
    }



}

