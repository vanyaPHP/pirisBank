<?php

namespace App\Controller;

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
        return $this->render('system_index.html.twig');
    }

    #[Route('/system/update-datetime', name: 'system_update_datetime', methods: 'POST')]
    public function setSystemDatetime(Request $request): RedirectResponse
    {
        $systemDateTime = $request->request->get('systemDateTime');
        $systemInformation = $this->entityManager->getRepository(SystemInformation::class)
            ->findBy([], ['id' => 'DESC'])[0];
        $systemInformation->setCurrentDate((new \DateTime())
            ->setTimestamp(strtotime($systemDateTime)));

        $this->entityManager->flush();

        return $this->redirect('http://localhost:8000/');
    }
}
