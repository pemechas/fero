<?php

namespace App\Tests\Service\Order;

use App\Service\Order\ProcessOrderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Order;
use App\DTO\CartDTO;
use App\DTO\ProductDTO;
use App\Enum\OrderStatus;

final class ProcessOrderServiceTest extends KernelTestCase
{
    private ProcessOrderService $processOrderService;

    protected function setUp(): void
    {
        /**
         * Boot the Symfony kernel
         */
        self::bootKernel();

        /**
         * Access the service container
         */
        $container = static::getContainer();

        /**
         * Get ProcessOrderService service
         */
        $this->processOrderService = $container->get(ProcessOrderService::class);

        $manager = $container->get('doctrine')->getManager();
        $orderRepository = $manager->getRepository(Order::class);

        /**
         * For some reason the transaction used in the extension "DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"
         * in "phpunit.dist.xml" is not working.
         * For simplicity I do the remove manually
         */
        foreach ($orderRepository->findAll() as $object) {
            $manager->remove($object);
        }

        $manager->flush();
    }

    public function testCalculateOrderPriceAndPersistSuccess(): void
    {
        $products = [];
        /**
         * Total for this product without taxes is 5
         */
        $product = new ProductDTO();
        $product->product_id = 1;
        $product->quantity = 2;
        $product->unit_price = 2.5;
        array_push($products, $product);

        /**
         * Total for this product without taxes is 10
         */
        $product = new ProductDTO();
        $product->product_id = 2;
        $product->quantity = 5;
        $product->unit_price = 2;
        array_push($products, $product);

        /**
         * Total cart without taxes is 15
         */
        $cart = new CartDTO();
        $cart->cart = $products;

        /**
         * Calculate prices with taxes (total 18)
         */
        $order = $this->processOrderService->calculateOrderPrice($cart);

        /** 
         * Id is null as it is not persisted yet
         */
        $this->assertEquals(get_class($order), Order::class);
        $this->assertNull($order->getId());
        $this->assertEquals($order->getTotalAmount(), 18);
        $this->assertEquals($order->getStatus(), OrderStatus::Pending);

        $this->processOrderService->persistOrder($order);
        $this->assertIsInt($order->getId());
    }
}
