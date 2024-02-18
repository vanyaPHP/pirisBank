<?php

namespace App\Domain\Service\Atm;

use App\Domain\Service\Account\AccountService;
use App\Domain\Service\Transaction\TransactionService;
use App\Entity\Account;
use App\Entity\Credit;
use Doctrine\ORM\EntityManagerInterface;

class AtmService implements AtmServiceInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function login(string $creditCardNumber, string $pinCode): string|Credit
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy([
                'creditCardNumber' => $creditCardNumber, 'creditCardPin' => $pinCode
        ]);

        if ($credit == null)
        {
            return 'Неверные данные';
        }

        return $credit;
    }

    public function withDrawMoney(int $creditId, float $amount): bool|string
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy(['id' => $creditId]);

        if ($credit->getMainAccount()->getBalance() < $amount)
        {
            return 'Недостаточно средств для выполнения транзакции';
        }

        $transactionService = new TransactionService($this->entityManager);
        $transactionService->commitTransaction(
            $credit->getMainAccount(),
            (new AccountService($this->entityManager))->getCashDeskAccount(),
            $amount
        );
        $transactionService->withDrawCashDeskTransaction($amount);

        $this->entityManager->flush();

        return true;
    }

    public function transferMoney(int $creditId, string $accountNumber, float $amount): bool|string
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy(['id' => $creditId]);
        $account = $this->entityManager->getRepository(Account::class)
            ->findOneBy(['accountNumber' => $accountNumber]);

        if ($account == null)
        {
            return 'Счёта с таким номером не существует';
        }

        if ($credit->getMainAccount()->getBalance() < $amount)
        {
            return 'Недостаточно средств для выполнения транзакции';
        }

        (new TransactionService($this->entityManager))->commitTransaction(
            $credit->getMainAccount(),
            $account,
            $amount
        );

        $this->entityManager->flush();

        return true;
    }
}