<?php

namespace App\Domain\Service\Account;

use App\Entity\Deposit;

interface AccountServiceInterface
{
    public function createAccountsForDeposit(Deposit $deposit): array;
    public function generateAccountNumber(string $accountType): string;
}