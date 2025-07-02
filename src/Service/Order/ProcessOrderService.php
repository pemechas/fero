<?php

namespace App\Service\Order;

use App\DTO\CartDTO;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class ProcessOrderService
{
    const FIXED_TAX = 0.2;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * It calculates the total amount price for the cart and returns an Order Entity
     *
     * @param CartDTO $cartDTO Valid cart dto with all the cart data
     * @return Order Order Entity ready to be persisted
     */
    public function calculateOrderPrice(CartDTO $cartDTO): Order
    {
        $products = $cartDTO->cart;

        $total_amount = 0;
        foreach($products as $product) {
            $price_before_tax = ($product->quantity * $product->unit_price);
            $price_with_tax = $price_before_tax + ($price_before_tax * self::FIXED_TAX);
            $total_amount += $price_with_tax;
        }

        $order = new Order();
        $order->setTotalAmount($total_amount);

        return $order;
    }

    /**
     * It saves the order entity in the database
     * 
     * @param Order $order Order entity
     */
    public function persistOrder(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
