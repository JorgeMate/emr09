<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sessions
 *
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="App\Repository\SessionsRepository")
 */
class Sessions
{
    /**
     * @var string
     *
     * @ORM\Column(name="sess_id", type="string", length=128, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessId;

    /**
     * @var string
     *
     * @ORM\Column(name="sess_data", type="blob", length=16777215, nullable=false)
     */
    private $sessData;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_time", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $sessTime;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_lifetime", type="integer", nullable=false)
     */
    private $sessLifetime;

    public function getSessId(): ?string
    {
        return $this->sessId;
    }

    public function getSessData()
    {
        return $this->sessData;
    }

    public function setSessData($sessData): self
    {
        $this->sessData = $sessData;

        return $this;
    }

    public function getSessTime(): ?int
    {
        return $this->sessTime;
    }

    public function setSessTime(int $sessTime): self
    {
        $this->sessTime = $sessTime;

        return $this;
    }

    public function getSessLifetime(): ?int
    {
        return $this->sessLifetime;
    }

    public function setSessLifetime(int $sessLifetime): self
    {
        $this->sessLifetime = $sessLifetime;

        return $this;
    }


}
