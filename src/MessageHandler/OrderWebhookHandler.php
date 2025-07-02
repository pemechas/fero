<?php

namespace App\MessageHandler;

use Exception;
use App\Message\OrderWebhookMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use App\Exception\OrderWebhookFailedException;
use App\Exception\OrderWebhookNotSentException;
use Symfony\Component\HttpFoundation\Response;

#[AsMessageHandler]
class OrderWebhookHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client,
        private LoggerInterface $logger
    ) {
    }

    /**
     * The message contains the order entity
     */
    public function __invoke(OrderWebhookMessage $message)
    {
        try {
            $order = $message->getOrder();

            $response = $this->client->request('POST', 'http://localhost:8000/webhook', [
                'json' => [
                    'id' => $order->getId(),
                    'total_amount' => $order->getTotalAmount(),
                    'status' => $order->getStatus(),
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== Response::HTTP_OK) {
                $this->logger->error('OrderWebhook was sent but failed.');
                throw new OrderWebhookFailedException($order->getId());
            }

            $this->logger->info('Order webhook for order with id ' . $order->getId() . ' was processed successfully.');
            
        } catch (Exception $e) {
            $this->logger->error('ERROR: OrderWebhook could not be sent: ' . $e->getMessage());
            throw new OrderWebhookNotSentException($message->getOrder()->getId());
        }
    }
}
