<?php

namespace App\Domain\Service\Credit;

use App\Domain\DTO\CreditDTO;
use App\Domain\Service\Account\AccountService;
use App\Domain\Service\SystemInformation\SystemInformationService;
use App\Domain\Service\Transaction\TransactionService;
use App\Entity\Credit;
use App\Repository\CreditRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreditService implements CreditServiceInterface
{
    public const CREDIT_CARD_NUMBER_LENGTH = 16;
    public const CREDIT_CARD_PIN_LENGTH = 4;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }


    public function create(CreditDTO $creditDTO): void
    {
        $credit = new Credit();
        $credit->setCreditPlan($creditDTO->creditPlan);
        $credit->setAmount($creditDTO->amount);
        $credit->setClient($creditDTO->client);
        $startDate = (new SystemInformationService($this->entityManager))->getCurrentBankDay();
        $endDate = ((new \DateTime())->setTimestamp(strtotime($startDate->format('Y-m-d'))))->modify("+{$creditDTO->creditPlan->getMonthPeriod()} month");
        $credit->setStartDate($startDate);
        $credit->setEndDate($endDate);
        $credit->setCreditNumber($this->generateCreditNumber($creditDTO));
        if ($creditDTO->isCreditCardNeeded)
        {
            $credentials = $this->generateCreditCardCredentials($creditDTO);
            $credit->setCreditCardNumber($credentials[0]);
            $credit->setCreditCardPin($credentials[1]);
        }
        else
        {
            $credit->setCreditNumber("0");
            $credit->setCreditCardPin("0");
        }

        $accounts = (new AccountService($this->entityManager))->createAccountsForCredit($credit);
        $credit->setMainAccount($accounts[0]);
        $credit->setPercentAccount($accounts[1]);

        $this->takeMoneyForCredit($credit);

        if (!$creditDTO->isCreditCardNeeded)
        {
            $this->withDrawCreditFromCashDesk($credit);
        }

        $this->entityManager->persist($credit);
        $this->entityManager->flush();
    }

    public function generateCreditNumber(CreditDTO $creditDTO): string
    {
        $lastCreditId = $this->entityManager->getRepository(Credit::class)
            ->findBy([], ['id' => 'DESC']);
        if (count($lastCreditId) > 0)
        {
            $lastCreditId = $lastCreditId[0]->getId();
        }
        else
        {
            $lastCreditId = 0;
        }

        $creditNumber = ($lastCreditId + 1) . $creditDTO->creditPlan->getId();
        $range = range('A', 'Z');
        for ($i = 0;$i < 20;$i++) {
            $creditNumber .= $range[rand(0, 25)];
        }

        return $creditNumber;
    }

    public function generateCreditCardCredentials(CreditDTO $creditDTO): array
    {
        $lastCreditId = $this->entityManager->getRepository(Credit::class)
            ->findBy([], ['id' => 'DESC']);
        if (count($lastCreditId) > 0)
        {
            $lastCreditId = $lastCreditId[0]->getId();
        }
        else
        {
            $lastCreditId = 0;
        }

        $creditCardNumber = (string)($lastCreditId + 1);
        $length = strlen($creditCardNumber);
        for($i = 0; $i < self::CREDIT_CARD_NUMBER_LENGTH - $length; $i++)
        {
            $creditCardNumber .= rand(0, 9);
        }

        $creditCardPin = ($lastCreditId + 1);
        $length = strlen($creditCardPin);
        for($i = 0; $i < self::CREDIT_CARD_PIN_LENGTH - $length; $i++)
        {
            $creditCardPin .= rand(0, 9);
        }

        return [$creditCardNumber, $creditCardPin];
    }

    public function closeBankDay(): void
    {
        /**
         * @var CreditRepository $creditRepository
         */
        $creditRepository = $this->entityManager->getRepository(Credit::class);
        $currentDate = (new SystemInformationService($this->entityManager))
            ->getCurrentBankDay();

        $activeCredits = $creditRepository->findActiveCredits($currentDate);
        foreach ($activeCredits as $activeCredit)
        {
            $this->commitPercents($activeCredit);
            $this->entityManager->flush();
        }
    }

    public function commitPercents(Credit $credit): void
    {
        $monthCount = $credit->getCreditPlan()->getMonthPeriod();
        $percentPerMonth = $credit->getCreditPlan()->getPercent() / 12;

        $currentDay = (new SystemInformationService($this->entityManager))->getCurrentBankDay();

        if ($credit->getCreditPlan()->isAnuity())
        {
            $anuityCoefficient =
            ($percentPerMonth * pow(1 + $percentPerMonth, $monthCount)) /
            (pow(1 + $percentPerMonth, $monthCount) - 1);

            $paymentPerMonth = $anuityCoefficient * (double)$credit->getAmount();
            $percentAmount = $paymentPerMonth / 30;
        }
        else
        {
            $creditRest = (double)$credit->getAmount();
            $dayReturningCreditBodyPart = (double)$credit->getAmount() / $monthCount / 30;
            $percentPerDay = $percentPerMonth / 30;

            for ($i = 0; $i < date_diff($currentDay, $credit->getStartDate())->days; $i++)
            {
                $creditRest -= $dayReturningCreditBodyPart;
            }

            $percentAmount = $dayReturningCreditBodyPart + $creditRest * $percentPerDay;
        }

        (new TransactionService($this->entityManager))->commitTransaction(
            $credit->getPercentAccount(),
            (new AccountService($this->entityManager))->getDevelopmentFundAccount(),
            $percentAmount
        );
    }

    public function payPercents(int $creditId): bool|string
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy(['id' => $creditId]);

        if ($credit->getAmount() == 0)
        {
            return 'Нельзя оплатить проценты по закрытому кредиту';
        }

        $amount = abs($credit->getPercentAccount()->getBalance());

        $transactionService = new TransactionService($this->entityManager);
        $transactionService->withDrawCashDeskTransaction($amount);
        $transactionService->commitTransaction(
            (new AccountService($this->entityManager))->getCashDeskAccount(),
            $credit->getPercentAccount(),
            $amount
        );

        $this->entityManager->flush();

        return true;
    }

    public function closeCredit(int $creditId): bool|string
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy(['id' => $creditId]);

        if ($credit->getEndDate() > (new SystemInformationService($this->entityManager))
                ->getCurrentBankDay())
        {
            return 'Нельзя закрыть кредит до окончания кредитного периода';
        }

        if ($credit->getAmount() == 0)
        {
            return 'Кредит уже был закрыт';
        }

        $transactionService = new TransactionService($this->entityManager);
        $accountService = new AccountService($this->entityManager);

        if (!$credit->getCreditPlan()->isAnuity())
        {
            $transactionService->commitCashDeskDebitTransaction($credit->getAmount());
            $transactionService->commitTransaction(
                $accountService->getCashDeskAccount(),
                $credit->getMainAccount(),
                $credit->getAmount()
            );
            $transactionService->commitTransaction(
                $credit->getMainAccount(),
                $accountService->getDevelopmentFundAccount(),
                $credit->getAmount()
            );
        }

        $transactionService->commitCashDeskDebitTransaction($credit
            ->getPercentAccount()->getBalance());
        $transactionService->commitTransaction(
            $accountService->getCashDeskAccount(),
            $credit->getPercentAccount(),
            $credit->getPercentAccount()->getBalance()
        );

        $credit->setAmount(0);

        $this->entityManager->flush();

        return true;
    }

    public function takeMoneyForCredit(Credit $credit): void
    {
        (new TransactionService($this->entityManager))
            ->commitTransaction(
                (new AccountService($this->entityManager))->getDevelopmentFundAccount(),
                $credit->getMainAccount(),
                $credit->getAmount()
            );
    }

    public function withDrawCreditFromCashDesk(Credit $credit): void
    {
        $transactionService = new TransactionService($this->entityManager);
        $transactionService->commitTransaction(
                $credit->getMainAccount(),
                (new AccountService($this->entityManager))->getCashDeskAccount(),
                $credit->getAmount()
        );

        $transactionService->withDrawCashDeskTransaction($credit->getAmount());
    }
}