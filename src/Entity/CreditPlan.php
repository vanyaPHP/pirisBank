<?php

namespace App\Entity;

use App\Repository\CreditPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreditPlanRepository::class)]
class CreditPlan
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

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2)]
    private ?string $minAmount = null;

    #[ORM\ManyToOne(inversedBy: 'mainCreditPlans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountPlan $mainAccountPlan = null;

    #[ORM\ManyToOne(inversedBy: 'percentCreditPlans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountPlan $percentAccountPlan = null;

    #[ORM\OneToMany(targetEntity: Credit::class, mappedBy: 'creditPlan')]
    private Collection $credits;

    #[ORM\Column]
    private ?bool $isAnuity = null;

    public function __construct()
    {
        $this->credits = new ArrayCollection();
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
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): static
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->setCreditPlan($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): static
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getCreditPlan() === $this) {
                $credit->setCreditPlan(null);
            }
        }

        return $this;
    }

    public function isAnuity(): ?bool
    {
        return $this->isAnuity;
    }

    public function setIsAnuity(bool $isAnuity): static
    {
        $this->isAnuity = $isAnuity;

        return $this;
    }
}
