<?php

namespace App\Domain\Service\SystemInformation;

use App\Entity\SystemInformation;
use Doctrine\ORM\EntityManagerInterface;

class SystemInformationService implements SystemInformationServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getCurrentBankDay(): \DateTime
    {
        return $this->entityManager->getRepository(SystemInformation::class)->findAll()[0]->getCurrentDate();
    }
}