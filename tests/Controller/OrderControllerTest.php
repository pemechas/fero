<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class OrderControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $orderRepository = $this->manager->getRepository(Order::class);

        /**
         * For some reason the transaction used in the extension "DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"
         * in "phpunit.dist.xml" is not working.
         * For simplicity I do the remove manually.
         */
        foreach ($orderRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testCheckoutSuccess(): void
    {
        $this->client->jsonRequest('POST', '/checkout', [
            'cart' => [
                [  
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price' => 2.5
                ]
            ]
        ]);

        self::assertResponseIsSuccessful();

        /**
         * Check that the message for the webhook is sent
         */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());
    }

    public function testCheckoutRequestMalformed(): void
    {
        $this->client->jsonRequest('POST', '/checkout', [
            'cart' => [
                [  
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price' => 'not decimal' // This must be decimal number
                ]
            ]
        ]);
        
        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertResponseIsUnprocessable();
        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('cart[0].unit_price', $response['errors']);

        /**
         * Check that no message for the webhook is sent
         */
        $transport = $this->getContainer()->get('messenger.transport.async');
        $this->assertCount(0, $transport->getSent());
    }
}
