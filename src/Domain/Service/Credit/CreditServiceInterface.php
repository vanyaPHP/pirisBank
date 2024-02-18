<?php

namespace App\Domain\Service\Credit;

use App\Domain\DTO\CreditDTO;
use App\Entity\Credit;

interface CreditServiceInterface
{
    public function create(CreditDTO $creditDTO): void;
    public function generateCreditNumber(CreditDTO $creditDTO): string;
    public function generateCreditCardCredentials(CreditDTO $creditDTO): array;
    public function closeBankDay(): void;
    public function commitPercents(Credit $credit): void;
    public function payPercents(int $creditId): bool|string;
    public function closeCredit(int $creditId): bool|string;
    public function takeMoneyForCredit(Credit $credit): void;
    public function withDrawCreditFromCashDesk(Credit $credit): void;
}