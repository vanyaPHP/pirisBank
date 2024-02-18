<?php

namespace App\Domain\Service\Deposit;

use App\Domain\DTO\DepositDTO;
use App\Entity\Deposit;

interface DepositServiceInterface
{
    public function create(DepositDTO $depositDTO): void;
    public function generateDepositNumber(DepositDTO $depositDTO): string;
    public function closeBankDay(): void;
    public function commitPercents(Deposit $deposit): void;
    public function closeDeposit(int $depositId): bool|string;
    public function withDrawPercents(int $depositId): bool|string;
}