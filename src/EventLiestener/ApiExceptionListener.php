<?php 


namespace App\EventLiestener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
    public function __onKernalException(ExceptionEvent $event): void
    {

        $exception = $event->getThrowable();

        $statusCode = JsonResponse :: HTTP_INTERNAL_SERVER_ERROR;

        if($exception instanceof HttpExceptionInterface){
            $statusCode = $exception->getStatusCode();

        }
        $responseData = [
            'success' => false,
            'error'  => [
                'message' => $exception->getMessage(),
                'code' => $statusCode
            ]
            ];

            $response = new JsonResponse($responseData,$statusCode);
            $event->setResponse($response);

    }
}