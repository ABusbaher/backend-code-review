<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\DTO\ListMessageDTO;
use App\Entity\Message;
use App\Enum\StatusMessage;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;
    private MessageRepository $messages;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        /* @phpstan-ignore-next-line */
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->messages = $this->entityManager->getRepository(Message::class);
    }

    public function test_it_has_connection(): void
    {
        $this->assertSame(10, count($this->messages->findAll()));
    }

    public function test_get_all_messages_paginated_without_status(): void
    {
        $dto = new ListMessageDTO(10, 1);
        $result = $this->messages->getAllMessagesPaginated($dto);

        $this->assertIsArray($result);
        $this->assertCount(10, $result['messages']);
        $this->assertEquals(10, $result['limit']);
        $this->assertEquals(1, $result['page']);
        $this->assertEquals(10, $result['totalItems']);
    }

    public function test_get_all_messages_paginated_with_status(): void
    {
        $dto = new ListMessageDTO(3, 2, StatusMessage::read->value);
        $result = $this->messages->getAllMessagesPaginated($dto);

        $this->assertIsArray($result);
        $this->assertEquals(3, $result['limit']);
        $this->assertEquals(2, $result['page']);
        foreach ($result['messages'] as $message) {
            $this->assertSame(StatusMessage::read, $message['status']);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager?->close();
        $this->entityManager = null;
    }
}