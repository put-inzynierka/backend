<?php

namespace App\Exception;

use Throwable;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException as CoreUnprocessableEntityHttpException;

class UnprocessableEntityHttpException extends CoreUnprocessableEntityHttpException
{
    private ConstraintViolationListInterface $violationsList;

    public function __construct(
        ConstraintViolationListInterface $list,
        string $message = 'Form contains errors.',
        Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        $this->violationsList = $list;

        parent::__construct($message, $previous, $code, $headers);
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->violationsList;
    }
}