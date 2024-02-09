<?php

namespace App\Domain\Service\Deposit;

use App\Domain\DTO\DepositDTO;

interface DepositServiceInterface
{
    public function create(DepositDTO $depositDTO): void;
    public function generateDepositNumber(DepositDTO $depositDTO): string;
}