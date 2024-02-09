<?php

namespace App\Entity;

use App\Repository\DepositRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepositRepository::class)]
class Deposit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $depositNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2)]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'deposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DepositPlan $depositPlan = null;

    #[ORM\ManyToOne(inversedBy: 'deposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'mainAccountDeposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $mainAccount = null;

    #[ORM\ManyToOne(inversedBy: 'percentAccountDeposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $percentAccount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepositNumber(): ?string
    {
        return $this->depositNumber;
    }

    public function setDepositNumber(string $depositNumber): static
    {
        $this->depositNumber = $depositNumber;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDepositPlan(): ?DepositPlan
    {
        return $this->depositPlan;
    }

    public function setDepositPlan(?DepositPlan $depositPlan): static
    {
        $this->depositPlan = $depositPlan;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getMainAccount(): ?Account
    {
        return $this->mainAccount;
    }

    public function setMainAccount(?Account $mainAccount): static
    {
        $this->mainAccount = $mainAccount;

        return $this;
    }

    public function getPercentAccount(): ?Account
    {
        return $this->percentAccount;
    }

    public function setPercentAccount(?Account $percentAccount): static
    {
        $this->percentAccount = $percentAccount;

        return $this;
    }
}
