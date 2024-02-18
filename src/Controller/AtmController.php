<?php

namespace App\Controller;

use App\Domain\Service\Atm\AtmService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AtmController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/atm', name: 'atm_index')]
    public function index(): Response
    {
        return $this->render('atm_index.html.twig');
    }

    #[Route('/atm/login', name: 'atm_login', methods: 'POST')]
    public function login(Request $request)
    {
        $request = $request->request->all();
        $result = (new AtmService($this->entityManager))
            ->login($request['cardNumber'], $request['pinCode']);

        if (is_string($result))
        {
            return $this->render('notification.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/atm',
                'hrefText' => 'Вернуться к странице авторизации'
            ]);
        }

        return $this->render('atm_work_page.html.twig', [
            'creditId' => $result->getId(),
            'balance' => $result->getMainAccount()->getBalance()
        ]);
    }

    #[Route('/atm/cash-page', name: 'atm_cash_page', methods: 'POST')]
    public function cashPage(Request $request): Response
    {
        return $this->render('atm_cash_page.html.twig', [
           'creditId' => $request->request->get('creditId')
        ]);
    }

    #[Route('/atm/transfer-page', name: 'atm_transfer_page', methods: 'POST')]
    public function transferPage(Request $request): Response
    {
        return $this->render('atm_transfer_page.html.twig', [
            'creditId' => $request->request->get('creditId')
        ]);
    }

    #[Route('/atm/cash', name: 'atm_cash', methods: 'POST')]
    public function cash(Request $request): Response
    {
        $request = $request->request->all();
        $result = (new AtmService($this->entityManager))
            ->withDrawMoney($request['creditId'], $request['amount']);

        if (is_string($result))
        {
            return $this->render('notification.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/atm',
                'hrefText' => 'Попробовать ещё раз'
            ]);
        }

        return $this->render('notification.html.twig', [
            'error' => 'Выдача наличных прошла успешно',
            'href' => 'http://localhost:8000/atm',
            'hrefText' => 'Продолжить операции'
        ]);
    }

    #[Route('/atm/transfer', name: 'atm_transfer', methods: 'POST')]
    public function transfer(Request $request)
    {
        $request = $request->request->all();
        $result = (new AtmService($this->entityManager))->transferMoney(
            $request['creditId'],
            $request['accountNumber'],
            $request['amount']
        );

        if (is_string($result))
        {
            return $this->render('notification.html.twig', [
                'error' => $result,
                'href' => 'http://localhost:8000/atm',
                'hrefText' => 'Попробовать ещё раз'
            ]);
        }

        return $this->render('notification.html.twig', [
            'error' => 'Перевод прошёл успешно',
            'href' => 'http://localhost:8000/atm',
            'hrefText' => 'Продолжить операции'
        ]);
    }
}
