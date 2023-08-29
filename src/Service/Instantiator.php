<?php

namespace App\Service;

use App\Component\Model\ContainmentValidationRule;
use App\Entity\Component\Contract\ContainmentValidatable;
use App\Exception\UnprocessableEntityHttpException;
use App\Service\Validation\ContainmentValidator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class Instantiator
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected ValidatorInterface $validator
    ) {}

    public function deserialize(
        string $data,
        string $class,
        string $group,
        $possessor = null,
        bool $validate = true
    ) {
        $options = [AbstractNormalizer::GROUPS => [$group]];
        if ($possessor) {
            $options[AbstractNormalizer::OBJECT_TO_POPULATE] = $possessor;
        }

        try {
            $instance = $this->serializer->deserialize(
                $data,
                $class,
                'json',
                $options
            );
        } catch (NotNormalizableValueException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        if ($validate) {
            $this->validate($instance, $group);
        }

        return $instance;
    }

    public function validate($instance, string $group): void
    {
        $violations = $this->validator->validate($instance, null, [$group, 'Default']);

        if ($violations->count() > 0) {
            throw new UnprocessableEntityHttpException($violations);
        }

        if ($instance instanceof ContainmentValidatable) {
            /** @var ContainmentValidationRule $rule */
            foreach ($instance->getContainmentValidationRules() as $rule) {
                /** @var ContainmentValidator $validator */
                $validator = $rule->getValidatorClass();

                $violations->addAll(
                    $validator::validate($rule->getNeedles(), $rule->getHaystack())
                );
            }
        }

        if ($violations->count() > 0) {
            throw new UnprocessableEntityHttpException($violations);
        }
    }
}
