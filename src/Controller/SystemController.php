<?php

namespace App\Controller;

use App\Domain\Service\Deposit\DepositService;
use App\Entity\SystemInformation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SystemController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/system', name: 'system_index')]
    public function index(): Response
    {
        $systemInformation = $this->entityManager->getRepository(SystemInformation::class)
            ->findBy([], ['id' => 'DESC'])[0];

        return $this->render('system_index.html.twig', [
            'systemDateTime' => $systemInformation->getCurrentDate()->format('Y F j e'),
            'systemDateTimeVar' => $systemInformation->getCurrentDate()->format('Y-m-d')
        ]);
    }

    #[Route('/system/update-datetime', name: 'system_update_datetime', methods: 'POST')]
    public function setSystemDatetime(Request $request): Response|RedirectResponse
    {
        $systemInformation = $this->entityManager->getRepository(SystemInformation::class)
            ->findBy([], ['id' => 'DESC'])[0];
        $systemDateTime = $request->request->get('systemDateTime');
        $depositService = new DepositService($this->entityManager);

        $newDate = (new \DateTime())->setTimestamp(strtotime($systemDateTime));
        $oldDate = $systemInformation->getCurrentDate();

        if ($newDate <= $oldDate)
        {
            return $this->render('error.html.twig', [
                'error' => 'Новая дата должны быть больше текущей',
                'href' => 'http://localhost:8000/system',
                'hrefText' => 'Вернуться на страницу системной информации'
            ]);
        }

        $daysPassed = date_diff($newDate, $oldDate)->days;
        for ($i = 0;$i < $daysPassed;$i++)
        {
            $depositService->closeBankDay();
            $systemInformation->setCurrentDate(((new \DateTime())
                ->setTimestamp(strtotime($systemInformation->getCurrentDate()->format('Y-m-d'))))
                ->modify("+1 day"));
            $this->entityManager->flush();
        }

        return $this->redirect('http://localhost:8000/');
    }
}
