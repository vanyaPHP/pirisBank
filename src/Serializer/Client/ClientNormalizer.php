<?php

namespace App\Serializer\Client;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class ClientNormalizer implements NormalizerInterface
{

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $reflection = new \ReflectionClass($object);
        $client = [];
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property)
        {
            if ($property->getName() == 'birthDate' ||
                $property->getName() == 'passportReleaseDate')
            {
                $client[$property->getName()] = $property->getValue($object)->format('Y-m-d');
            }
            else
            {
                $client[$property->getName()] = $property->getValue($object);
            }
        }
        $client['liveCity'] = [];
        $reflection = new \ReflectionClass($object->getLiveCity());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property)
        {
            if ($property->getName() != 'clients')
            {
                $client['liveCity'][$property->getName()] = $property->getValue($object->getLiveCity());
            }
        }

        $client['citizenship'] = [];
        $reflection = new \ReflectionClass($object->getCitizenship());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property)
        {
            if ($property->getName() != 'clients')
            {
                $client['citizenship'][$property->getName()] = $property->getValue($object->getCitizenship());
            }
        }

        $client['disability'] = [];
        $reflection = new \ReflectionClass($object->getDisability());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property)
        {
            if ($property->getName() != 'clients')
            {
                $client['disability'][$property->getName()] = $property->getValue($object->getDisability());
            }
        }

        $client['familyStatus'] = [];
        $reflection = new \ReflectionClass($object->getFamilyStatus());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property)
        {
            if ($property->getName() != 'clients')
            {
                $client['familyStatus'][$property->getName()] = $property->getValue($object->getFamilyStatus());
            }
        }

        return $client;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return true;
    }

    public function __call(string $name, array $arguments)
    {

    }
}