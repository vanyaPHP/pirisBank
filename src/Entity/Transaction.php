<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $transactionDay = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 5)]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'debitTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $debitAccount = null;

    #[ORM\ManyToOne(inversedBy: 'creditTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $creditAccount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionDay(): ?\DateTimeInterface
    {
        return $this->transactionDay;
    }

    public function setTransactionDay(\DateTimeInterface $transactionDay): static
    {
        $this->transactionDay = $transactionDay;

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

    public function getDebitAccount(): ?Account
    {
        return $this->debitAccount;
    }

    public function setDebitAccount(?Account $debitAccount): static
    {
        $this->debitAccount = $debitAccount;

        return $this;
    }

    public function getCreditAccount(): ?Account
    {
        return $this->creditAccount;
    }

    public function setCreditAccount(?Account $creditAccount): static
    {
        $this->creditAccount = $creditAccount;

        return $this;
    }
}
