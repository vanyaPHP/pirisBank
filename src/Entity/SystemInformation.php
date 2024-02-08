<?php

namespace App\Entity;

use App\Repository\SystemInformationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemInformationRepository::class)]
class SystemInformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $currentDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentDate(): ?\DateTimeInterface
    {
        return $this->currentDate;
    }

    public function setCurrentDate(\DateTimeInterface $currentDate): static
    {
        $this->currentDate = $currentDate;

        return $this;
    }
}
