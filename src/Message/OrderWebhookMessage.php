<?php

namespace App\Message;

use App\Entity\Order;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
class OrderWebhookMessage
{
    public function __construct(
        private Order $order,
    ) {}

    public function getOrder(): Order
    {
        return $this->order;
    }
}
