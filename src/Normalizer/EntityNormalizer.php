<?php

namespace App\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityNormalizer implements DenormalizerInterface
{
    protected EntityManagerInterface $entityManager;
    protected ObjectNormalizer $objectNormalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectNormalizer $objectNormalizer
    ) {
        $this->entityManager = $entityManager;
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        $isAnEntity = str_starts_with($type, 'App\\Entity\\');

        $hasId =
            is_array($data) &&
            ((array_key_exists('id', $data) && !is_null($data['id'])) ||
            (array_key_exists('uuid', $data) && !is_null($data['uuid'])))
        ;

        return $isAnEntity && $hasId;
    }

    public function denormalize($data, string $class, string $format = null, array $context = [])
    {
        $identifier = array_key_exists('id', $data) ? 'id' : 'uuid';

        $databaseInstance = $this->entityManager->find($class, $data[$identifier]);
        $context[ObjectNormalizer::OBJECT_TO_POPULATE] = $databaseInstance;

        return $this->objectNormalizer->denormalize($data, $class, $format, $context);
    }
}