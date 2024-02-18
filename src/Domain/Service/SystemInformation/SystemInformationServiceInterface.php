<?php

namespace App\Domain\Service\SystemInformation;

interface SystemInformationServiceInterface
{
    public function getCurrentBankDay(): \DateTime;
}