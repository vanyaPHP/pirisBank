<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 13)]
    private ?string $accountNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 5)]
    private ?string $debitValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 5)]
    private ?string $creditValue = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 5)]
    private ?string $balance = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AccountPlan $accountPlan = null;

    #[ORM\OneToMany(targetEntity: Deposit::class, mappedBy: 'mainAccount')]
    private Collection $mainAccountDeposits;

    #[ORM\OneToMany(targetEntity: Deposit::class, mappedBy: 'percentAccount')]
    private Collection $percentAccountDeposits;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'debitAccount')]
    private Collection $debitTransactions;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'creditAccount')]
    private Collection $creditTransactions;

    public function __construct()
    {
        $this->mainAccountDeposits = new ArrayCollection();
        $this->percentAccountDeposits = new ArrayCollection();
        $this->debitTransactions = new ArrayCollection();
        $this->creditTransactions = new ArrayCollection();
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

    public function getDebitValue(): ?string
    {
        return $this->debitValue;
    }

    public function setDebitValue(string $debitValue): static
    {
        $this->debitValue = $debitValue;

        return $this;
    }

    public function getCreditValue(): ?string
    {
        return $this->creditValue;
    }

    public function setCreditValue(string $creditValue): static
    {
        $this->creditValue = $creditValue;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getAccountPlan(): ?AccountPlan
    {
        return $this->accountPlan;
    }

    public function setAccountPlan(?AccountPlan $accountPlan): static
    {
        $this->accountPlan = $accountPlan;

        return $this;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function getMainAccountDeposits(): Collection
    {
        return $this->mainAccountDeposits;
    }

    public function addMainAccountDeposit(Deposit $mainAccountDeposit): static
    {
        if (!$this->mainAccountDeposits->contains($mainAccountDeposit)) {
            $this->mainAccountDeposits->add($mainAccountDeposit);
            $mainAccountDeposit->setMainAccount($this);
        }

        return $this;
    }

    public function removeMainAccountDeposit(Deposit $mainAccountDeposit): static
    {
        if ($this->mainAccountDeposits->removeElement($mainAccountDeposit)) {
            // set the owning side to null (unless already changed)
            if ($mainAccountDeposit->getMainAccount() === $this) {
                $mainAccountDeposit->setMainAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Deposit>
     */
    public function getPercentAccountDeposits(): Collection
    {
        return $this->percentAccountDeposits;
    }

    public function addPercentAccountDeposit(Deposit $percentAccountDeposit): static
    {
        if (!$this->percentAccountDeposits->contains($percentAccountDeposit)) {
            $this->percentAccountDeposits->add($percentAccountDeposit);
            $percentAccountDeposit->setPercentAccount($this);
        }

        return $this;
    }

    public function removePercentAccountDeposit(Deposit $percentAccountDeposit): static
    {
        if ($this->percentAccountDeposits->removeElement($percentAccountDeposit)) {
            // set the owning side to null (unless already changed)
            if ($percentAccountDeposit->getPercentAccount() === $this) {
                $percentAccountDeposit->setPercentAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getDebitTransactions(): Collection
    {
        return $this->debitTransactions;
    }

    public function addDebitTransaction(Transaction $debitTransaction): static
    {
        if (!$this->debitTransactions->contains($debitTransaction)) {
            $this->debitTransactions->add($debitTransaction);
            $debitTransaction->setDebitAccount($this);
        }

        return $this;
    }

    public function removeDebitTransaction(Transaction $debitTransaction): static
    {
        if ($this->debitTransactions->removeElement($debitTransaction)) {
            // set the owning side to null (unless already changed)
            if ($debitTransaction->getDebitAccount() === $this) {
                $debitTransaction->setDebitAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getCreditTransactions(): Collection
    {
        return $this->creditTransactions;
    }

    public function addCreditTransaction(Transaction $creditTransaction): static
    {
        if (!$this->creditTransactions->contains($creditTransaction)) {
            $this->creditTransactions->add($creditTransaction);
            $creditTransaction->setCreditAccount($this);
        }

        return $this;
    }

    public function removeCreditTransaction(Transaction $creditTransaction): static
    {
        if ($this->creditTransactions->removeElement($creditTransaction)) {
            // set the owning side to null (unless already changed)
            if ($creditTransaction->getCreditAccount() === $this) {
                $creditTransaction->setCreditAccount(null);
            }
        }

        return $this;
    }
}
