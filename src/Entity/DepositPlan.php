<?php

namespace App\Entity;

use App\Repository\DepositPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepositPlanRepository::class)]
class DepositPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $monthPeriod = null;

    #[ORM\Column]
    private ?float $percent = null;

    #[ORM\Column]
    private ?bool $isRevocable = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2)]
    private ?string $minAmount = null;

    #[ORM\ManyToOne(inversedBy: 'MainAccountPlanDeposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountPlan $mainAccountPlan = null;

    #[ORM\ManyToOne(inversedBy: 'percentAccountPlanDeposits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountPlan $percentAccountPlan = null;

    #[ORM\OneToMany(targetEntity: Deposit::class, mappedBy: 'depositPlan')]
    private Collection $deposits;

    public function __construct()
    {
        $this->deposits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMonthPeriod(): ?int
    {
        return $this->monthPeriod;
    }

    public function setMonthPeriod(int $monthPeriod): static
    {
        $this->monthPeriod = $monthPeriod;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function isIsRevocable(): ?bool
    {
        return $this->isRevocable;
    }

    public function setIsRevocable(bool $isRevocable): static
    {
        $this->isRevocable = $isRevocable;

        return $this;
    }

    public function getMinAmount(): ?string
    {
        return $this->minAmount;
    }

    public function setMinAmount(string $minAmount): static
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    public function getMainAccountPlan(): ?AccountPlan
    {
        return $this->mainAccountPlan;
    }

    public function setMainAccountPlan(?AccountPlan $mainAccountPlan): static
    {
        $this->mainAccountPlan = $mainAccountPlan;

        return $this;
    }

    public function getPercentAccountPlan(): ?AccountPlan
    {
        return $this->percentAccountPlan;
    }

    public function setPercentAccountPlan(?AccountPlan $percentAccountPlan): static
    {
        $this->percentAccountPlan = $percentAccountPlan;

        return $this;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function getDeposits(): Collection
    {
        return $this->deposits;
    }

    public function addDeposit(Deposit $deposit): static
    {
        if (!$this->deposits->contains($deposit)) {
            $this->deposits->add($deposit);
            $deposit->setDepositPlan($this);
        }

        return $this;
    }

    public function removeDeposit(Deposit $deposit): static
    {
        if ($this->deposits->removeElement($deposit)) {
            // set the owning side to null (unless already changed)
            if ($deposit->getDepositPlan() === $this) {
                $deposit->setDepositPlan(null);
            }
        }

        return $this;
    }
}
