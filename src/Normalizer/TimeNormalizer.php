<?php

namespace App\Normalizer;

use DateTimeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class TimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->format('H:i');
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof DateTimeInterface && $data->format('Y-m-d') === '1970-01-01';
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): \DateTimeInterface
    {
        if (\DateTime::class === $type) {
            return new \DateTime('1970-01-01 ' . $data . ':00');
        }

        return new \DateTimeImmutable('1970-01-01 ' . $data . ':00');
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return is_string($data) && preg_match('/^\d{2}\:\d{2}$/', $data);
    }
}