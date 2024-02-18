<?php

namespace App\Domain\Service\Atm;

use App\Entity\Credit;

interface AtmServiceInterface
{
    public function login(string $creditCardNumber, string $pinCode): string|Credit;
    public function withDrawMoney(int $creditId, float $amount): bool|string;
    public function transferMoney(int $creditId, string $accountNumber, float $amount): bool|string;
}