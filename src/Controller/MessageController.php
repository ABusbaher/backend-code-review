<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\ListMessageDTO;
use App\Interface\MessageRepositoryInterface;
use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class MessageController extends AbstractController
{
    public function __construct(private readonly MessageRepositoryInterface $messageRepository, private readonly MessageBusInterface $bus)
    {
    }

    #[Route('/messages', methods: ['POST'] )]
    public function list(
        #[MapRequestPayload] ListMessageDTO $listMessageDTO
    ): JsonResponse
    {
        $messages = $this->messageRepository->getAllMessagesPaginated($listMessageDTO);

        return new JsonResponse($messages, Response::HTTP_OK);
    }

    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request): Response
    {
        $text = $request->get('text');
        
        if (!is_string($text) || trim($text) === '') {
            return new Response('Text is required', Response::HTTP_BAD_REQUEST);
        }

        $this->bus->dispatch(new SendMessage($text));

        return new Response('Successfully sent', Response::HTTP_CREATED);
    }
}