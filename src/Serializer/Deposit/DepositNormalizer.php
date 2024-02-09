<?php

namespace App\Serializer\Deposit;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @method array getSupportedTypes(?string $format)
 */
class DepositNormalizer implements NormalizerInterface
{

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $depositPlan = [];
        foreach ($properties as $property)
        {
            $depositPlan[$property->getName()] = $property->getValue($object);
        }

        return $depositPlan;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return true;
    }

    public function __call(string $name, array $arguments)
    {

    }
}