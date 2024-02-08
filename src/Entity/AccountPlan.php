<?php

namespace App\Entity;

use App\Repository\AccountPlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountPlanRepository::class)]
class AccountPlan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $accountName = null;

    #[ORM\Column(length: 1)]
    private ?string $accountType = null;

    #[ORM\OneToMany(targetEntity: DepositPlan::class, mappedBy: 'mainAccountPlan')]
    private Collection $mainAccountPlanDeposits;

    #[ORM\OneToMany(targetEntity: DepositPlan::class, mappedBy: 'percentAccountPlan')]
    private Collection $percentAccountPlanDeposits;

    #[ORM\OneToMany(targetEntity: Account::class, mappedBy: 'accountPlan')]
    private Collection $accounts;

    public function __construct()
    {
        $this->mainAccountPlanDeposits = new ArrayCollection();
        $this->percentAccountPlanDeposits = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): static
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): static
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * @return Collection<int, DepositPlan>
     */
    public function getMainAccountPlanDeposits(): Collection
    {
        return $this->mainAccountPlanDeposits;
    }

    public function addMainAccountPlanDeposit(DepositPlan $mainAccountPlanDeposit): static
    {
        if (!$this->mainAccountPlanDeposits->contains($mainAccountPlanDeposit)) {
            $this->mainAccountPlanDeposits->add($mainAccountPlanDeposit);
            $mainAccountPlanDeposit->setMainAccountPlan($this);
        }

        return $this;
    }

    public function removeMainAccountPlanDeposit(DepositPlan $mainAccountPlanDeposit): static
    {
        if ($this->mainAccountPlanDeposits->removeElement($mainAccountPlanDeposit)) {
            // set the owning side to null (unless already changed)
            if ($mainAccountPlanDeposit->getMainAccountPlan() === $this) {
                $mainAccountPlanDeposit->setMainAccountPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DepositPlan>
     */
    public function getPercentAccountPlanDeposits(): Collection
    {
        return $this->percentAccountPlanDeposits;
    }

    public function addPercentAccountPlanDeposit(DepositPlan $percentAccountPlanDeposit): static
    {
        if (!$this->percentAccountPlanDeposits->contains($percentAccountPlanDeposit)) {
            $this->percentAccountPlanDeposits->add($percentAccountPlanDeposit);
            $percentAccountPlanDeposit->setPercentAccountPlan($this);
        }

        return $this;
    }

    public function removePercentAccountPlanDeposit(DepositPlan $percentAccountPlanDeposit): static
    {
        if ($this->percentAccountPlanDeposits->removeElement($percentAccountPlanDeposit)) {
            // set the owning side to null (unless already changed)
            if ($percentAccountPlanDeposit->getPercentAccountPlan() === $this) {
                $percentAccountPlanDeposit->setPercentAccountPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setAccountPlan($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getAccountPlan() === $this) {
                $account->setAccountPlan(null);
            }
        }

        return $this;
    }
}
