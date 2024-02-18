<?php

namespace App\Domain\Service\Account;

use App\Entity\Account;
use App\Entity\AccountPlan;
use App\Entity\Credit;
use App\Entity\Deposit;
use Doctrine\ORM\EntityManagerInterface;

class AccountService implements AccountServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createAccountsForDeposit(Deposit $deposit): array
    {
        $mainAccount = new Account();
        $mainAccount->setAccountPlan($deposit->getDepositPlan()->getMainAccountPlan());
        $mainAccount->setBalance(0);
        $mainAccount->setCreditValue(0);
        $mainAccount->setDebitValue(0);
        $mainAccount->setAccountNumber($this->generateAccountNumber('M'));

        $percentAccount = new Account();
        $percentAccount->setAccountPlan($deposit->getDepositPlan()->getPercentAccountPlan());
        $percentAccount->setBalance(0);
        $percentAccount->setDebitValue(0);
        $percentAccount->setCreditValue(0);
        $percentAccount->setAccountNumber($this->generateAccountNumber('P'));

        $this->entityManager->persist($mainAccount);
        $this->entityManager->persist($percentAccount);

        return [
            0 => $mainAccount,
            1 => $percentAccount
        ];
    }

    public function createAccountsForCredit(Credit $credit): array
    {
        $mainAccount = new Account();
        $mainAccount->setAccountPlan($credit->getCreditPlan()->getMainAccountPlan());
        $mainAccount->setBalance(0);
        $mainAccount->setCreditValue(0);
        $mainAccount->setDebitValue(0);
        $mainAccount->setAccountNumber($this->generateAccountNumber('CM'));

        $percentAccount = new Account();
        $percentAccount->setAccountPlan($credit->getCreditPlan()->getPercentAccountPlan());
        $percentAccount->setBalance(0);
        $percentAccount->setDebitValue(0);
        $percentAccount->setCreditValue(0);
        $percentAccount->setAccountNumber($this->generateAccountNumber('CP'));

        $this->entityManager->persist($mainAccount);
        $this->entityManager->persist($percentAccount);

        return [
            0 => $mainAccount,
            1 => $percentAccount
        ];
    }

    public function generateAccountNumber(string $accountType): string
    {
        $lastAccountId = $this->entityManager->getRepository(Account::class)
            ->findBy([], ['id' => 'DESC']);
        if (count($lastAccountId) > 0)
        {
            $lastAccountId = $lastAccountId[0]->getId();
        }
        else
        {
            $lastAccountId = 0;
        }

        $accountNumber = ($lastAccountId + 1) . $accountType;
        $leftLength = 13 - strlen($accountNumber);
        $mask = '';
        for ($i = 0;$i < $leftLength;$i++)
        {
            $mask .= '0';
        }

        return $mask . $accountNumber;
    }

    public function getCashDeskAccount(): Account
    {
        return $this->entityManager->getRepository(Account::class)
            ->findOneBy(
                ['accountPlan' => $this->entityManager->getRepository(AccountPlan::class)
                ->findOneBy(['accountNumber' => '1010'])]
            );
    }

    public function getDevelopmentFundAccount(): Account
    {
        return $this->entityManager->getRepository(Account::class)
            ->findOneBy(
                ['accountPlan' => $this->entityManager->getRepository(AccountPlan::class)
                ->findOneBy(['accountNumber' => '7327'])]);
    }
}