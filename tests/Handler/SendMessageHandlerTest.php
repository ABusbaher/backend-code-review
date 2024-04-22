<?php
declare(strict_types=1);

namespace App\Tests\Handler;

use App\Entity\Message;
use App\Message\SendMessageHandler;
use App\Message\SendMessage;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SendMessageHandlerTest extends TestCase
{
    public function test_send_message_handler(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('persist')->with(
            $this->callback(function($sendMessage){
                return $sendMessage instanceof Message &&
                    $sendMessage->getText() === 'Hello, world!';
            }));
        $entityManager->expects(self::once())->method('flush');

        $sendMessage = new SendMessage('Hello, world!');
        $handler = new SendMessageHandler($entityManager);
        $handler->__invoke($sendMessage);
    }
}