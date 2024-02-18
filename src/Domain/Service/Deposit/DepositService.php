<?php

namespace App\Domain\Service\Deposit;

use App\Domain\DTO\DepositDTO;
use App\Domain\Service\Account\AccountService;
use App\Domain\Service\SystemInformation\SystemInformationService;
use App\Domain\Service\Transaction\TransactionService;
use App\Entity\Deposit;
use App\Repository\DepositRepository;
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
        $startDate = (new SystemInformationService($this->entityManager))->getCurrentBankDay();
        $endDate = ((new \DateTime())->setTimestamp(strtotime($startDate->format('Y-m-d'))))->modify("+{$depositDTO->depositPlan->getMonthPeriod()} month");
        $deposit->setStartDate($startDate);
        $deposit->setEndDate($endDate);

        $accountService = new AccountService($this->entityManager);
        $accounts = $accountService->createAccountsForDeposit($deposit);
        $deposit->setMainAccount($accounts[0]);
        $deposit->setPercentAccount($accounts[1]);

        $transactionService = new TransactionService($this->entityManager);
        $transactionService->commitCashDeskDebitTransaction($deposit->getAmount());
        $transactionService->commitTransaction($accountService->getCashDeskAccount(),
            $deposit->getMainAccount(), $deposit->getAmount());
        $transactionService->commitTransaction($deposit->getMainAccount(),
            $accountService->getDevelopmentFundAccount(), $deposit->getAmount());

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

    public function closeBankDay(): void
    {
        /**
         * @var DepositRepository $depositRepository
         */
        $depositRepository = $this->entityManager->getRepository(Deposit::class);
        $systemInformationService = new SystemInformationService($this->entityManager);
        $currentDate = $systemInformationService->getCurrentBankDay();

        $activeDeposits = $depositRepository->findActiveDeposits($currentDate);
        foreach ($activeDeposits as $activeDeposit)
        {
            $this->commitPercents($activeDeposit);
            $this->entityManager->flush();
        }
    }

    public function commitPercents(Deposit $deposit): void
    {
        $percentAmount = $deposit->getAmount() *
            ($deposit->getDepositPlan()->getPercent() / 100 / 365);

        (new TransactionService($this->entityManager))->commitTransaction(
            (new AccountService($this->entityManager))->getDevelopmentFundAccount(),
            $deposit->getPercentAccount(),
            $percentAmount
        );
    }

    public function closeDeposit(int $depositId): bool|string
    {
        $accountService = new AccountService($this->entityManager);
        $transactionService = new TransactionService($this->entityManager);
        $deposit = $this->entityManager->getRepository(Deposit::class)
            ->findOneBy(['id' => $depositId]);

        if (!$deposit->getDepositPlan()->getIsRevocable()
            &&
            $deposit->getEndDate() >
            (
                new SystemInformationService($this->entityManager))
                ->getCurrentBankDay()
            )
        {
            return 'Депозит не может быть закрыт до окончания срока';
        }

        if ($deposit->getAmount() == 0)
        {
            return 'Депозит уже был закрыт';
        }

        $transactionService->commitTransaction(
            $accountService->getDevelopmentFundAccount(),
            $deposit->getMainAccount(),
            $deposit->getAmount()
        );

        $transactionService->commitTransaction(
            $deposit->getMainAccount(),
            $accountService->getCashDeskAccount(),
            $deposit->getAmount()
        );

        $percentBalance = $deposit->getPercentAccount()->getBalance();

        $transactionService->commitTransaction(
            $deposit->getPercentAccount(),
            $accountService->getCashDeskAccount(),
            $percentBalance
        );

        $transactionService->withDrawCashDeskTransaction(
            $deposit->getMainAccount()->getCreditValue() + $percentBalance);
        $deposit->setAmount(0);

        $this->entityManager->flush();

        return true;
    }

    public function withDrawPercents(int $depositId): bool|string
    {
        $systemInformationService = new SystemInformationService($this->entityManager);
        $transactionService = new TransactionService($this->entityManager);
        $deposit = $this->entityManager->getRepository(Deposit::class)
            ->findOneBy(['id' => $depositId]);

        if ($deposit->getDepositPlan()->getIsRevocable())
        {
            return 'Нельзя отозвать проценты до окончания депозитной программы';
        }

        if (date_diff($systemInformationService->getCurrentBankDay(),
                      $deposit->getStartDate())
            ->days
            % 30 != 0)
        {
            return 'Нельзя отозвать проценты до окончания месяца';
        }

        $percentAmount = $deposit->getPercentAccount()->getBalance();

        $transactionService->commitTransaction(
            $deposit->getPercentAccount(),
            (new AccountService($this->entityManager))->getCashDeskAccount(),
            $percentAmount
        );

        $transactionService->withDrawCashDeskTransaction($percentAmount);

        $this->entityManager->flush();

        return true;
    }
}