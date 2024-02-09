<?php

namespace App\Controller;

use App\Domain\DTO\DepositDTO;
use App\Domain\Service\Deposit\DepositService;
use App\Entity\Client;
use App\Entity\DepositPlan;
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

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new DepositNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/deposits', name: 'deposit_index')]
    public function index(): Response
    {
        return $this->render('deposit_index.html.twig');
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

    #[Route('/deposits/store', name: 'deposit_store', methods: 'POST')]
    public function store(Request $request): RedirectResponse|Response
    {
        $depositPlan = $this->entityManager->getRepository(DepositPlan::class)
            ->find($request->request->get('depositPlanId'));

        if ($request->request->get('amount') < $depositPlan->getMinAmount())
        {
            return $this->render('error.html.twig', [
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
}
