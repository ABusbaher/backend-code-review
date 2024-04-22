<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\Message;
use App\Enum\StatusMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class SendMessageHandler
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }
    
    public function __invoke(SendMessage $sendMessage): Message
    {
        // Save message.
        $message = new Message();
        $message->setUuid(Uuid::v6()->toRfc4122());
        $message->setText($sendMessage->text);
        $message->setStatus(StatusMessage::sent);
        $message->setCreatedAt(new \DateTime());

        $this->manager->persist($message);
        $this->manager->flush();
        return $message;
    }
}