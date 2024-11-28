<?php

namespace App\EventListener;

use App\Service\BlindIndexService;
use App\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;

#[AsEventListener(event: ExceptionEvent::class, method: 'onKernelException')]
class ApiExceptionListener
{
    public function __construct(private LoggerInterface $logger)
    {
        
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $result = [];
        $exception = $event->getThrowable();

        $message = $event->getThrowable()->getMessage();

        $code = method_exists($exception, 'getStatusCode') ? 
            $exception->getStatusCode() :
            500;
        dd($exception);
        switch ($exception) {
            case $exception instanceof NotFoundHttpException:
                $apiException = new ApiException(Response::HTTP_NOT_FOUND, ApiException::TYPE_NOT_FOUND);
                break;
            case $exception instanceof NotNormalizableValueException:
                $apiException = new ApiException(Response::HTTP_BAD_REQUEST, ApiException::TYPE_VALIDATION_ERROR);
                break;
            case $exception instanceof ValidationFailedException:
                $apiException = new ApiException(Response::HTTP_BAD_REQUEST, ApiException::TYPE_VALIDATION_ERROR);
                $violations = iterator_to_array($exception->getViolations()->getIterator());
                $simplifiedViolations = array_map(function(ConstraintViolation $violation): string {
                    $value = array_key_exists('{{ value }}', $violation->getParameters()) ? $violation->getParameters()['{{ value }}'] : '';
                    return $violation->getMessage() . " : " . $value . " at " . $violation->getPropertyPath();
                }, $violations);
                $message = $simplifiedViolations;
                break;
            case $exception instanceof \TypeError:
                $apiException = new ApiException(Response::HTTP_BAD_REQUEST, ApiException::TYPE_VALIDATION_ERROR);
                break;
            default:
                $message = ApiException::TYPE_INTERNAL_ERROR;
                $apiException = new ApiException($code, ApiException::TYPE_INTERNAL_ERROR);
                break;
        }

        $apiException->set('error', $message);

        $this->logger->error((string)$event->getThrowable());
        $result = $apiException->toArray();
        $event->setResponse(new JsonResponse($result, $apiException->getStatusCode()));
    }
}