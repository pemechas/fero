<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class WebhookController extends AbstractController
{
    #[Route('/webhook', name: 'webhook', methods: ['POST'])]
    public function webhook(): JsonResponse
    {
        $random_error = rand(0, 1);
        if ($random_error === 0) {
            return $this->json([
                'status' => 'failure',
                'message' => 'Ops! Something went wrong!',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'status' => 'success',
            'message' => 'All good!',
        ], Response::HTTP_OK);
    }
}
