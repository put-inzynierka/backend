<?php

namespace App\Normalizer;

use App\Entity\File\File;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FileNormalizer implements NormalizerInterface
{
    public function __construct(
        protected ObjectNormalizer $objectNormalizer,
        protected string $fileUrlFormat
    ) {}

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof File;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var File $object */
        $object->setUrl(
            sprintf($this->fileUrlFormat, $object->getUuid()->jsonSerialize())
        );

        $this->objectNormalizer->normalize($object, $format, $context);
    }
}