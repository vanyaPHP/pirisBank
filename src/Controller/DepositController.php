<?php

namespace App\Controller;

use App\Domain\DTO\DepositDTO;
use App\Domain\Service\Deposit\DepositService;
use App\Entity\Client;
use App\Entity\Deposit;
use App\Entity\DepositPlan;
use App\Entity\Transaction;
use App\Serializer\Deposit\DepositNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class DepositController extends AbstractController
{
    private readonly Serializer $serializer;
    private Deposit $deposit;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new DepositNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/deposits', name: 'deposit_index')]
    public function index(): Response
    {
        $depositsInfo = $this->entityManager->getRepository(Deposit::class)->findAll();
        $deposits = [];
        foreach ($depositsInfo as $depositInfo)
        {
            /**
             * @var Deposit $depositInfo
             */
            $deposit = [];
            $deposit['depositId'] = $depositInfo->getId();
            $deposit['number'] = $depositInfo->getDepositNumber();
            $deposit['amount'] = $depositInfo->getAmount();
            $deposit['client'] = "{$depositInfo->getClient()->getFirstName()} 
                {$depositInfo->getClient()->getMiddleName()}
                {$depositInfo->getClient()->getLastName()} 
                ({$depositInfo->getClient()->getPassportId()})";
            $deposit['startDate'] = $depositInfo->getStartDate()->format('Y-m-d');
            $deposit['endDate'] = $depositInfo->getEndDate()->format('Y-m-d');

            $deposits []= $deposit;
        }

        return $this->render('deposit_index.html.twig', [
            'deposits' => $deposits
        ]);
    }

    #[Route('/deposits/new', name: 'deposit_new')]
    public function new(): Response
    {
        $depositPlans = $this->entityManager->getRepository(DepositPlan::class)
            ->findAll();
        $clients = $this->entityManager->getRepository(Client::class)
            ->findAll();

        return $this->render('deposit_new.html.twig', [
            'depositPlans' => $depositPlans,
            'clients' => $clients
        ]);
    }

    #[Route('/deposits/details', name: 'deposit_details', methods: 'POST')]
    public function details(Request $request): Response
    {
        $deposit = $this->entityManager->getRepository(Deposit::class)
            ->findOneBy(['id' => $request->request->get('depositId')]);
        $this->deposit = $deposit;

        $data = [];
        $data['mainAccountDebit'] = $deposit->getMainAccount()->getDebitValue();
        $data['mainAccountCredit'] = $deposit->getMainAccount()->getCreditValue();
        $data['mainAccountSum'] = $deposit->getMainAccount()->getBalance();
        $plusTransactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['debitAccount' => $deposit->getMainAccount()]);
        $minusTransactions =  $this->entityManager->getRepository(Transaction::class)
            ->findBy(['creditAccount' => $deposit->getMainAccount()]);
        $data['mainAccountTransactions'] = array_merge($plusTransactions, $minusTransactions);
        usort($data['mainAccountTransactions'], [self::class,
            "sortTransactionsByDateTime"]);
        $data['mainAccountTransactions'] = array_map([self::class, "transactionToInfo"],
            $data['mainAccountTransactions']);

        $data['percentAccountDebit'] = $deposit->getPercentAccount()->getDebitValue();
        $data['percentAccountCredit'] = $deposit->getPercentAccount()->getCreditValue();
        $data['percentAccountSum'] = $deposit->getPercentAccount()->getBalance();
        $plusTransactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['debitAccount' => $deposit->getPercentAccount()]);
        $minusTransactions =  $this->entityManager->getRepository(Transaction::class)
            ->findBy(['creditAccount' => $deposit->getPercentAccount()]);
        $data['percentAccountTransactions'] = array_merge($plusTransactions, $minusTransactions);
        usort($data['percentAccountTransactions'],
            [self::class, "sortTransactionsByDateTime"]);
        $data['percentAccountTransactions'] = array_map([self::class, "transactionToInfo"],
            $data['percentAccountTransactions']);

        return $this->render('deposit_details.html.twig', [
            'deposit' => $data
        ]);
    }

    private function sortTransactionsByDateTime(Transaction $firstTransaction, Transaction $secondTransaction): int
    {
        if ($firstTransaction->getTransactionDay()
            == $secondTransaction->getTransactionDay())
        {
            return 0;
        }

        return ($firstTransaction->getTransactionDay()
            < $secondTransaction->getTransactionDay())
            ? -1
            : 1;
    }

    private function transactionToInfo(Transaction $transaction): array
    {
        if ($transaction->getCreditAccount()->getId()
            == $this->deposit->getMainAccount()->getId()
            ||
            $transaction->getDebitAccount()->getId()
            == $this->deposit->getMainAccount()->getId()
        )
        {
            if ($transaction->getCreditAccount()->getId()
                == $this->deposit->getMainAccount()->getId())
            {
                return [
                    'sign' => '+',
                    'amount' => $transaction->getAmount(),
                    'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
                ];
            }

            return [
                'sign' => '-',
                'amount' => $transaction->getAmount(),
                'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
            ];
        }
        else
        {
            if ($transaction->getCreditAccount()->getId()
                == $this->deposit->getPercentAccount()->getId())
            {
                return [
                    'sign' => '+',
                    'amount' => $transaction->getAmount(),
                    'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
                ];
            }

            return [
                'sign' => '-',
                'amount' => $transaction->getAmount(),
                'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
            ];
        }
    }

    #[Route('/deposits/store', name: 'deposit_store', methods: 'POST')]
    public function store(Request $request): RedirectResponse|Response
    {
        $depositPlan = $this->entityManager->getRepository(DepositPlan::class)
            ->find($request->request->get('depositPlanId'));

        if ($request->request->get('amount') < $depositPlan->getMinAmount())
        {
            return $this->render('notification.html.twig', [
                'error' => 'Сумма должна быть минимум ' .
                    $depositPlan->getMinAmount() . ' бел. рублей',
                'href' => 'http://localhost:8000/deposits/new',
                'hrefText' => 'Попробовать ещё раз'
            ]);
        }

        $depositService = new DepositService($this->entityManager);
        $depositService->create(
            new DepositDTO($depositPlan,
                $request->request->get('amount'),
                $this->entityManager->getRepository(Client::class)
                    ->findOneBy(['id' => $request->request->get('clientId')])
        ));

        return $this->redirect('http://localhost:8000/deposits');
    }

    #[Route('/deposits/take-percents', name: 'deposit_take_percents')]
    public function takePercents(Request $request): RedirectResponse|Response
    {
        $result = (new DepositService($this->entityManager))
            ->withDrawPercents($request->request->get('depositId'));

        if (is_string($result))
        {
            return $this->render('notification.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/deposits',
                'hrefText' => 'Вернуться к списку депозитов'
            ]);
        }

        return $this->redirect('http://localhost:8000/deposits');
    }

    #[Route('/deposits/close', name: 'deposit_close')]
    public function closeDeposit(Request $request): RedirectResponse|Response
    {
        $result = (new DepositService($this->entityManager))
            ->closeDeposit($request->request->get('depositId'));

        if (is_string($result))
        {
            return $this->render('notification.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/deposits',
                'hrefText' => 'Вернуться к списку депозитов'
            ]);
        }

        return $this->redirect('http://localhost:8000/deposits');
    }
}
