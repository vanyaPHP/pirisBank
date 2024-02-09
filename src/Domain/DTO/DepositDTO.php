<?php

namespace App\Domain\DTO;

use App\Entity\Client;
use App\Entity\Deposit;
use App\Entity\DepositPlan;

class DepositDTO
{
    public function __construct(
        public readonly DepositPlan $depositPlan,
        public readonly float $amount,
        public readonly Client $client
    )
    {
    }
}