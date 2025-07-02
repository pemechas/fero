<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * https://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers
 */
class HandleJsonResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent'
        ];
    }

    /**
     * Event to catch exceptions and return a proper json instead of html errors when
     * dto is incorrect in the controller
     */
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $isHttpEvent = $event->getThrowable() instanceof HttpExceptionInterface;
        $isValidationEvent = $event->getThrowable()->getPrevious() instanceof ValidationFailedException;

        // We are only interested in validation errors in an httpException context
        if (!$isHttpEvent || !$isValidationEvent) {
            return;
        }

        /**
         * @var ValidationFailedException $validationException
         */
        $validationException = $event->getThrowable()->getPrevious();
        $errorMessages = [];
        foreach ($validationException->getViolations() as $violation) {
            $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $event->setResponse(new JsonResponse(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
