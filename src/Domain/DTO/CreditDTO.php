<?php

namespace App\Domain\DTO;

use App\Entity\Client;
use App\Entity\CreditPlan;

class CreditDTO
{
    public function __construct(
        public readonly CreditPlan $creditPlan,
        public readonly float $amount,
        public readonly bool $isCreditCardNeeded,
        public readonly Client $client
    )
    {
    }
}