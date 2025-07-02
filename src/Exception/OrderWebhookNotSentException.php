<?php

namespace App\Exception;

use Exception;

class OrderWebhookNotSentException extends Exception
{
    public function __construct(int $order_id)
    {
        parent::__construct('Order webhook for order with id ' . $order_id . ' could not be sent');
    }
}
