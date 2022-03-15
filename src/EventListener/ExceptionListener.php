<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class ExceptionListener
{
    public function __construct(
        protected CamelCaseToSnakeCaseNameConverter $converter
    ) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $object = $event->getThrowable();
        $statusCode = null;
        $headers = [];

        if ($object instanceof HttpException) {
            $statusCode = $object->getStatusCode();
            $headers = $object->getHeaders();
        } elseif ($object instanceof AccessDeniedException) {
            $statusCode = Response::HTTP_FORBIDDEN;
        } elseif ($object instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            'status' => $statusCode,
            'message' => $object->getMessage(),
        ];

        if ($object instanceof UnprocessableEntityHttpException) {
            foreach ($object->getConstraintViolationList() as $violation) {
                $data['violations'][] = [
                    'path' => $this->converter->normalize($violation->getPropertyPath()),
                    'message' => $violation->getMessage(),
                ];
            }
        } else {
            $traceKeys = array_flip(['class', 'type', 'function', 'file', 'line']);

            foreach ($object->getTrace() as $trace) {
                $data['trace'][] = array_intersect_key($trace, $traceKeys);
            }
        }

        $response = new JsonResponse(
            $data,
            $statusCode,
            $headers
        );

        $event->setResponse($response);
    }
}