<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserDocRepository")
 */
class UserDoc
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CenterDocGroup", inversedBy="userDocs")
     */
    private $centerdocgroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userDocs")
     */
    private $user;


/**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_files", mimeType="mime_type", fileNameProperty="docName", size="imageSize")
     * 
     * @var File
     */
    private $docFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $docName;

    /**
     * @ORM\Column(type="integer")
     */
    private $docSize;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCenterdocgroup(): ?CenterDocGroup
    {
        return $this->centerdocgroup;
    }

    public function setCenterdocgroup(?CenterDocGroup $centerdocgroup): self
    {
        $this->centerdocgroup = $centerdocgroup;

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


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $docFile
     */
    public function setDocFile(?File $docFile = null): void
    {
        $this->docFile = $docFile;

        if (null !== $docFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getDocFile(): ?File
    {
        return $this->DocFile;
    }







    public function getDocName(): ?string
    {
        return $this->docName;
    }

    public function setDocName(string $docName): self
    {
        $this->docName = $docName;

        return $this;
    }

    public function getDocSize(): ?int
    {
        return $this->docSize;
    }

    public function setDocSize(int $docSize): self
    {
        $this->docSize = $docSize;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
