<?php

namespace App\Domain\Service\Transaction;

use App\Entity\Account;

interface TransactionServiceInterface
{
    public function commitTransaction(Account $debitAccount, Account $creditAccount, string $amount): void;
    public function commitCashDeskDebitTransaction(string $amount): void;
    public function withDrawCashDeskTransaction(string $amount): void;
}