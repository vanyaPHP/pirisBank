<?php

namespace App\Domain\Service\Deposit;

use App\Domain\DTO\DepositDTO;
use App\Domain\Service\Account\AccountService;
use App\Entity\Deposit;
use Doctrine\ORM\EntityManagerInterface;

class DepositService implements DepositServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(DepositDTO $depositDTO): void
    {
        $deposit = new Deposit();
        $deposit->setDepositNumber($this->generateDepositNumber($depositDTO));
        $deposit->setAmount($depositDTO->amount);
        $deposit->setDepositPlan($depositDTO->depositPlan);
        $deposit->setClient($depositDTO->client);
        $startDate = new \DateTime();
        $deposit->setStartDate($startDate);
        $deposit->setEndDate($startDate
            ->modify("+{$depositDTO->depositPlan->getMonthPeriod()} month"));

        $accountService = new AccountService($this->entityManager);
        $accounts = $accountService->createAccountsForDeposit($deposit);
        $deposit->setMainAccount($accounts[0]);
        $deposit->setPercentAccount($accounts[1]);

        $this->entityManager->persist($deposit);
        $this->entityManager->flush();
    }

    public function generateDepositNumber(DepositDTO $depositDTO): string
    {
        $lastDepositId = $this->entityManager->getRepository(Deposit::class)
            ->findBy([], ['id' => 'DESC']);
        if (count($lastDepositId) > 0)
        {
            $lastDepositId = $lastDepositId[0]->getId();
        }
        else
        {
            $lastDepositId = 0;
        }

        $depositNumber = ($lastDepositId + 1) . $depositDTO->depositPlan->getId();
        $range = range('A', 'Z');
        for ($i = 0;$i < 20;$i++) {
            $depositNumber .= $range[rand(0, 25)];
        }

        return $depositNumber;
    }
}