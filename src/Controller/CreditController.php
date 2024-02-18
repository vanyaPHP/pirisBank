<?php

namespace App\Controller;

use App\Domain\DTO\CreditDTO;
use App\Domain\Service\Credit\CreditService;
use App\Entity\Client;
use App\Entity\Credit;
use App\Entity\CreditPlan;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreditController extends AbstractController
{
    private Credit $credit;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/credits', name: 'credit_index')]
    public function index(): Response
    {
        $creditsInfo = $this->entityManager->getRepository(Credit::class)
            ->findAll();

        $credits = [];

        foreach ($creditsInfo as $creditInfo)
        {
            $credit = [];
            $credit['creditId'] = $creditInfo->getId();
            $credit['number'] = $creditInfo->getCreditNumber();
            $credit['amount'] = $creditInfo->getAmount();
            $credit['client'] = "{$creditInfo->getClient()->getFirstName()} 
                {$creditInfo->getClient()->getMiddleName()}
                {$creditInfo->getClient()->getLastName()} 
                ({$creditInfo->getClient()->getPassportId()})";
            $credit['startDate'] = $creditInfo->getStartDate()->format('Y-m-d');
            $credit['endDate'] = $creditInfo->getEndDate()->format('Y-m-d');;

            if ($creditInfo->getCreditCardNumber() == "0")
            {
                $credit['cardNumber'] = "Карта по данному кредиту не создавалась";
                $credit['cardPin'] = "Карта по данному кредиту не создавалась";
            }
            else
            {
                $credit['cardNumber'] = $creditInfo->getCreditCardNumber();
                $credit['cardPin'] = $creditInfo->getCreditCardPin();
            }

            $credits []= $credit;
        }

        return $this->render('credit_index.html.twig', [
            'credits' => $credits
        ]);
    }

    #[Route('/credits/new', name: 'credit_new')]
    public function new(): Response
    {
        $clients = $this->entityManager->getRepository(Client::class)
            ->findAll();
        $creditPlans = $this->entityManager->getRepository(CreditPlan::class)
            ->findAll();

        return $this->render('credit_new.html.twig', [
            'clients' => $clients,
            'creditPlans' => $creditPlans
        ]);
    }

    #[Route('/credits/store', name: 'credit_store', methods: 'POST')]
    public function store(Request $request): RedirectResponse|Response
    {
        $request = $request->request->all();
        $creditPlan = $this->entityManager->getRepository(CreditPlan::class)
            ->findOneBy(['id' => $request['creditPlanId']]);

        if ($request['amount'] < $creditPlan->getMinAmount())
        {
            return $this->render('error.html.twig', [
                'error' => 'Сумма должна быть минимум ' .
                    $creditPlan->getMinAmount() . ' бел. рублей',
                'href' => 'http://localhost:8000/credits/new',
                'hrefText' => 'Попробовать ещё раз'
            ]);
        }

        $creditDTO = new CreditDTO(
            $this->entityManager->getRepository(CreditPlan::class)
                ->findOneBy(['id' => $request['creditPlanId']]),
            $request['amount'],
            isset ($request['isCardNeeded']),
            $this->entityManager->getRepository(Client::class)
                ->findOneBy(['id' => $request['clientId']]),
        );
        (new CreditService($this->entityManager))->create($creditDTO);

        return $this->redirect('http://localhost:8000/credits');
    }

    #[Route('/credits/details', name: 'credit_details', methods: 'POST')]
    public function details(Request $request)
    {
        $credit = $this->entityManager->getRepository(Credit::class)
            ->findOneBy(['id' => $request->request->get('creditId')]);
        $this->credit = $credit;

        $data = [];
        $data['mainAccountDebit'] = $credit->getMainAccount()->getDebitValue();
        $data['mainAccountCredit'] = $credit->getMainAccount()->getCreditValue();
        $data['mainAccountSum'] = $credit->getMainAccount()->getBalance();
        $plusTransactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['debitAccount' => $credit->getMainAccount()]);
        $minusTransactions =  $this->entityManager->getRepository(Transaction::class)
            ->findBy(['creditAccount' => $credit->getMainAccount()]);
        $data['mainAccountTransactions'] = array_merge($plusTransactions, $minusTransactions);
        usort($data['mainAccountTransactions'], [self::class,
            "sortTransactionsByDateTime"]);
        $data['mainAccountTransactions'] = array_map([self::class, "transactionToInfo"],
            $data['mainAccountTransactions']);

        $data['percentAccountDebit'] = $credit->getPercentAccount()->getDebitValue();
        $data['percentAccountCredit'] = $credit->getPercentAccount()->getCreditValue();
        $data['percentAccountSum'] = $credit->getPercentAccount()->getBalance();
        $plusTransactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['debitAccount' => $credit->getPercentAccount()]);
        $minusTransactions =  $this->entityManager->getRepository(Transaction::class)
            ->findBy(['creditAccount' => $credit->getPercentAccount()]);
        $data['percentAccountTransactions'] = array_merge($plusTransactions, $minusTransactions);
        usort($data['percentAccountTransactions'],
            [self::class, "sortTransactionsByDateTime"]);
        $data['percentAccountTransactions'] = array_map([self::class, "transactionToInfo"],
            $data['percentAccountTransactions']);

        return $this->render('credit_details.html.twig', [
            'credit' => $data
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
            == $this->credit->getMainAccount()->getId()
            ||
            $transaction->getDebitAccount()->getId()
            == $this->credit->getMainAccount()->getId()
        )
        {
            if ($transaction->getCreditAccount()->getId()
                == $this->credit->getMainAccount()->getId())
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
                == $this->credit->getPercentAccount()->getId())
            {
                return [
                    'sign' => '-',
                    'amount' => $transaction->getAmount(),
                    'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
                ];
            }

            return [
                'sign' => '+',
                'amount' => $transaction->getAmount(),
                'datetime' => $transaction->getTransactionDay()->format('Y-m-d')
            ];
        }
    }

    #[Route('/credits/pay-percents', name: 'credit_pay_percents', methods: 'POST')]
    public function payPercents(Request $request): RedirectResponse|Response
    {
        $result = (new CreditService($this->entityManager))
            ->payPercents($request->request->get('creditId'));

        if (is_string($result))
        {
            return $this->render('error.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/credits',
                'hrefText' => 'Вернуться к списку кредитов'
            ]);
        }

        return $this->redirect('http://localhost:8000/credits');
    }

    #[Route('/credits/close', name: 'credit_close', methods: 'POST')]
    public function closeCredit(Request $request): RedirectResponse|Response
    {
        $result = (new CreditService($this->entityManager))
            ->closeCredit($request->request->get('creditId'));

        if (is_string($result))
        {
            return $this->render('error.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/credits',
                'hrefText' => 'Вернуться к списку кредитов'
            ]);
        }

        return $this->redirect('http://localhost:8000/credits');
    }
}
