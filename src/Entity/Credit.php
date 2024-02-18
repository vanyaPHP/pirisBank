<?php

namespace App\Entity;

use App\Repository\CreditRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreditRepository::class)]
class Credit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $creditNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(length: 16)]
    private ?string $creditCardNumber = null;

    #[ORM\Column(length: 4)]
    private ?string $creditCardPin = null;

    #[ORM\ManyToOne(inversedBy: 'mainAccountCredits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $mainAccount = null;

    #[ORM\ManyToOne(inversedBy: 'percentAccountCredits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $percentAccount = null;

    #[ORM\ManyToOne(inversedBy: 'credits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'credits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreditPlan $creditPlan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreditNumber(): ?string
    {
        return $this->creditNumber;
    }

    public function setCreditNumber(string $creditNumber): static
    {
        $this->creditNumber = $creditNumber;

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

    public function getCreditCardNumber(): ?string
    {
        return $this->creditCardNumber;
    }

    public function setCreditCardNumber(string $creditCardNumber): static
    {
        $this->creditCardNumber = $creditCardNumber;

        return $this;
    }

    public function getCreditCardPin(): ?string
    {
        return $this->creditCardPin;
    }

    public function setCreditCardPin(string $creditCardPin): static
    {
        $this->creditCardPin = $creditCardPin;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCreditPlan(): ?CreditPlan
    {
        return $this->creditPlan;
    }

    public function setCreditPlan(?CreditPlan $creditPlan): static
    {
        $this->creditPlan = $creditPlan;

        return $this;
    }
}
