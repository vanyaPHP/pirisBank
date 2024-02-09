<?php

namespace App\Controller;

use App\Entity\Citizenship;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Disability;
use App\Entity\FamilyStatus;
use Doctrine\ORM\EntityManagerInterface;
use App\Serializer\Client\ClientDenormalizer;
use App\Serializer\Client\ClientNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class ClientController extends AbstractController
{
    private readonly Serializer $serializer;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ClientDenormalizer($this->entityManager), new ClientNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route('/clients', name: 'client_index')]
    public function index(): Response
    {
        $clients = $this->entityManager->getRepository(Client::class)->findBy([], ['lastName' => 'ASC']);
        $data = [];
        foreach ($clients as $client)
        {
            $data []= json_decode($this->serializer->serialize($client, 'json'),true);
        }

        return $this->render('client_index.html.twig', [
            'clients' => $data
        ]);
    }

    #[Route('/clients/new', name:'client_new')]
    public function new(): Response
    {
        $cities = $this->entityManager->getRepository(City::class)->findAll();
        $disabilities = $this->entityManager->getRepository(Disability::class)->findAll();
        $familyStatuses = $this->entityManager->getRepository(FamilyStatus::class)->findAll();
        $citizenships = $this->entityManager->getRepository(Citizenship::class)->findAll();

        return $this->render('client_new.html.twig', [
            'cities' => $cities,
            'citizenships' => $citizenships,
            'familyStatuses' => $familyStatuses,
            'disabilities' => $disabilities
        ]);
    }

    #[Route('/clients/store', name: 'client_store', methods: 'POST')]
    public function store(Request $request)
    {
        $data = $request->request->all();
        if (!$this->isFioValid($data['firstName'],
            $data['middleName'], $data['lastName']))
        {
            return $this->render('error.html.twig', [
                'error' => 'Проверьте ФИО: неверный формат или уже существует',
                'href' => "http://localhost:8000/clients/new",
                'hrefText' => 'Попробовать ещё раз'
            ]);
        }
        if (!$this->isPassportValid($data))
        {
            return $this->render('error.html.twig', [
               'error' => 'Данные паспорта невалидны или такой пользователь уже есть',
               'href' => "http://localhost:8000/clients/new",
               'hrefText' => 'Попробовать ещё раз'
            ]);
        }

        $client = $this->serializer->denormalize($data, Client::class);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->redirect('http://localhost:8000/clients');
    }

    #[Route('/clients/edit', name: 'client_edit', methods: 'POST')]
    public function edit(Request $request): Response
    {
        $client = $this->entityManager->getRepository(Client::class)->find($request->request->get('clientId'));
        $client = json_decode($this->serializer->serialize($client, 'json'), true);
        $cities = $this->entityManager->getRepository(City::class)->findAll();
        $disabilities = $this->entityManager->getRepository(Disability::class)->findAll();
        $familyStatuses = $this->entityManager->getRepository(FamilyStatus::class)->findAll();
        $citizenships = $this->entityManager->getRepository(Citizenship::class)->findAll();

        return $this->render('client_edit.html.twig', [
            'client' => $client,
            'cities' => $cities,
            'disabilities' => $disabilities,
            'familyStatuses' => $familyStatuses,
            'citizenships' => $citizenships
        ]);
    }

    #[Route('/clients/update', name: 'client_update', methods: 'POST')]
    public function update(Request $request)
    {
        $client = $this->entityManager->getRepository(Client::class)
            ->find($request->request->get('clientId'));

        $client->setLiveCity($this->entityManager->getRepository(City::class)
            ->find($request->request->get('liveCity')));
        $client->setCurrentLiveAddress($request->request->get('currentLiveAddress'));
        $client->setFamilyStatus($this->entityManager->getRepository(FamilyStatus::class)
            ->find($request->request->get('familyStatus')));
        $client->setDisability($this->entityManager->getRepository(Disability::class)
            ->find($request->request->get('disability')));
        $client->setCitizenship($this->entityManager->getRepository(Citizenship::class)
            ->find($request->request->get('citizenship')));
        $client->setPassportReleaseDate((new \DateTime())
            ->setTimestamp(strtotime($request->request->get('passportReleaseDate'))));
        $client->setRegistrationAddress($request->request->get('registrationAddress'));
        $client->setHomePhone($request->request->get('homePhone'));
        $client->setMobilePhone($request->request->get('mobilePhone'));
        $client->setEmail($request->request->get('email'));
        $client->setIsPensioner($request->request->get('isPensioner'));
        $client->setMonthSalary($request->request->get('monthSalary'));

        $this->entityManager->flush();

        return $this->redirect('http://localhost:8000/clients');
    }

    #[Route('/clients/delete', name: 'client_delete', methods: 'POST')]
    public function delete(Request $request): RedirectResponse
    {
        $clientId = $request->request->get('clientId');
        $client = $this->entityManager->getRepository(Client::class)->find($clientId);
        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $this->redirect('http://localhost:8000/clients');
    }

    private function isFioValid(string $firstName, string $middleName, string $lastName): bool
    {
        $notAlphabet = ['0', '1', '2', '3', '4', '5',
            '6', '7', '8', '9', ' ', '.', ',', '^', '*', '(', ')', '-'];

        foreach ($notAlphabet as $symbol)
        {
            if (str_contains($firstName, $symbol)
                || str_contains($middleName, $symbol)
                || str_contains($lastName, $symbol))
            {
                return false;
            }
        }

        $clients = $this->entityManager->getRepository(Client::class)->findBy([
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName
        ]);
        if (count($clients) > 0)
        {
            return false;
        }

        return true;
    }

    private function isPassportValid(array $data): bool
    {
        $samePasswordClients = $this->entityManager->getRepository(Client::class)->findBy([
            'passportSeries' => $data['passportSeries'],
            'passportNum' => $data['passportNum'],
            'passportId' => $data['passportId']
        ]);

        if (count($samePasswordClients) > 0)
        {
            return false;
        }

        $samePassportIdClients = $this->entityManager->getRepository(Client::class)->findBy([
            'passportId' => $data['passportId']
        ]);
        if (count($samePassportIdClients) > 0)
        {
            return false;
        }

        return true;
    }
}
