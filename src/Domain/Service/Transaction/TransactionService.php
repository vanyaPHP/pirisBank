<?php

namespace App\Domain\Service\Transaction;

use App\Domain\Service\Account\AccountService;
use App\Domain\Service\SystemInformation\SystemInformationService;
use App\Entity\Account;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function commitTransaction(Account $debitAccount, Account $creditAccount, string $amount): void
    {
        if ($debitAccount->getAccountPlan()->getAccountType() == 'P')
        {
            $debitAccount
                ->setDebitValue($debitAccount->getDebitValue() + $amount);
            $debitAccount
                ->setBalance($debitAccount->getCreditValue()
                    - $debitAccount->getDebitValue());
        }
        else
        {
            $debitAccount
                ->setCreditValue($debitAccount->getCreditValue() + $amount);
            $debitAccount
                ->setBalance($debitAccount->getDebitValue()
                    - $debitAccount->getCreditValue());
        }

        if ($creditAccount->getAccountPlan()->getAccountType() == 'P')
        {
            $creditAccount
                ->setCreditValue($creditAccount->getCreditValue() + $amount);
            $creditAccount->setBalance($creditAccount->getCreditValue()
                    - $creditAccount->getDebitValue());
        }
        else
        {
            $creditAccount
                ->setDebitValue($creditAccount->getDebitValue() + $amount);
            $creditAccount->setBalance($creditAccount->getDebitValue()
                - $creditAccount->getCreditValue());
        }

        $transaction = new Transaction();
        $transaction->setTransactionDay(
            (new SystemInformationService($this->entityManager))
            ->getCurrentBankDay());
        $transaction->setAmount($amount);
        $transaction->setDebitAccount($debitAccount);
        $transaction->setCreditAccount($creditAccount);

        $this->entityManager->persist($transaction);
    }

    public function commitCashDeskDebitTransaction(string $amount): void
    {
        $cashDeskAccount = (new AccountService($this->entityManager))
            ->getCashDeskAccount();
        $cashDeskAccount->setDebitValue($cashDeskAccount->getDebitValue() + $amount);
        $cashDeskAccount->setBalance($cashDeskAccount->getDebitValue()
            - $cashDeskAccount->getCreditValue());
    }

    public function withDrawCashDeskTransaction(string $amount): void
    {
        $cashDeskAccount = (new AccountService($this->entityManager))
            ->getCashDeskAccount();

        $cashDeskAccount
            ->setCreditValue($cashDeskAccount->getCreditValue() + $amount);
        $cashDeskAccount
            ->setBalance($cashDeskAccount->getDebitValue()
                - $cashDeskAccount->getCreditValue());
    }
}