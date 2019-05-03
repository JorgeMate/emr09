<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserLogRepository")
 */
class UserLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $login_time;

    /**
     * @ORM\Column(type="string", length=63)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $agent;

    /**
     * @Gedmo\Timestampable(on="change", field="logout_type")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $logout_time;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $logout_type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoginTime(): ?\DateTimeInterface
    {
        return $this->login_time;
    }

    public function setLoginTime(\DateTimeInterface $login_time): self
    {
        $this->login_time = $login_time;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getLogoutTime(): ?\DateTimeInterface
    {
        return $this->logout_time;
    }

    public function setLogoutTime(\DateTimeInterface $logout_time): self
    {
        $this->logout_time = $logout_time;

        return $this;
    }

    public function getLogoutType(): ?int
    {
        return $this->logout_type;
    }

    public function setLogoutType(?int $logout_type): self
    {
        $this->logout_type = $logout_type;

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
