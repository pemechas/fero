<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\Order\ProcessOrderService;
use App\DTO\CartDTO;
use Exception;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\OrderWebhookMessage;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class OrderController extends AbstractController
{
    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(
        #[MapRequestPayload] CartDTO $cartDTO,
        ProcessOrderService $processOrderService,
        MessageBusInterface $bus,
        LoggerInterface $logger,
        EntityManagerInterface $manager,
    ): JsonResponse
    {
        $manager->getConnection()->beginTransaction();
        try {
            $orderEntity = $processOrderService->calculateOrderPrice($cartDTO);
            $processOrderService->persistOrder($orderEntity);

            /**
             * Message to call the webhook asynchronously
             */
            $bus->dispatch(new OrderWebhookMessage($orderEntity));

            $manager->getConnection()->commit();

            return $this->json([
                'status' => 'success',
                'message' => 'Order Created successfully',
            ], Response::HTTP_OK);

        } catch(Exception $e) {
            $manager->getConnection()->rollBack();

            $logger->error('ERROR: checkout: Order creation failed: ' . $e->getMessage());

            return $this->json([
                'status' => 'failure',
                'message' => 'Order creation failed',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
