<?php

namespace App\Serializer\Client;

use App\Entity\Citizenship;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Disability;
use App\Entity\FamilyStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class ClientDenormalizer implements DenormalizerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Client
    {
        $client = new Client();
        $client->setFirstName($data['firstName']);
        $client->setMiddleName($data['middleName']);
        $client->setLastName($data['lastName']);
        $client->setSex($data['sex']);
        $client->setMonthSalary($data['monthSalary']);
        $client->setRegistrationAddress($data['registrationAddress']);
        $client->setIsPensioner($data['isPensioner']);
        $client->setEmail($data['email']);
        $client->setHomePhone($data['homePhone']);
        $client->setMobilePhone($data['mobilePhone']);
        $client->setBirthPlace($data['birthPlace']);
        $client->setCurrentLiveAddress($data['currentLiveAddress']);
        $client->setPassportReleaseDate((new \DateTime())->setTimestamp(strtotime($data['passportReleaseDate'])));
        $client->setPassportId($data['passportId']);
        $client->setPassportProvider($data['passportProvider']);
        $client->setPassportNum($data['passportNum']);
        $client->setPassportSeries($data['passportSeries']);
        $client->setBirthDate((new \DateTime())->setTimestamp(strtotime($data['birthDate'])));
        $client->setLiveCity($this->entityManager->getRepository(City::class)
            ->findOneBy(['id' => $data['liveCity']]));
        $client->setDisability($this->entityManager->getRepository(Disability::class)
            ->findOneBy(['id' => $data['disability']]));
        $client->setCitizenship($this->entityManager->getRepository(Citizenship::class)
            ->findOneBy(['id' => $data['citizenship']]));
        $client->setFamilyStatus($this->entityManager->getRepository(FamilyStatus::class)
            ->findOneBy(['id' => $data['familyStatus']]));

        return $client;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null)
    {
        return true;
    }

    public function __call(string $name, array $arguments)
    {

    }
}