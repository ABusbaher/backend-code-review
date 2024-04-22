<?php

namespace App\Repository;

use App\DTO\ListMessageDTO;
use App\Entity\Message;
use App\Interface\MessageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository implements MessageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return  array{
     *     messages: list<array{
     *         uuid: string,
     *         text: string,
     *         status: string
     *     }>,
     *     page: int,
     *     limit: int,
     *     totalItems: int
     * }
     */
    public function getAllMessagesPaginated(ListMessageDTO $listMessageDTO): array
    {
        $limit = (int) $listMessageDTO->getLimit();
        $page = (int) $listMessageDTO->getPage();
        $status = $listMessageDTO->getStatus();
        $queryBuilder = $this->createQueryBuilder('m');
        $expr = $queryBuilder->expr();

        // Get all messages filtered by status if it is provided.
        if ($status) {
            $queryBuilder
                ->andWhere($expr->eq('m.status', ':status'))
                ->setParameter('status', $status);
        }

        // Paginate messages
        $paginator = new Paginator($queryBuilder);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $totalItems = $paginator->count();

        // Prepare messages in requested format.
        $messages = [];
        foreach ($paginator as $message) {
            $messages[] = [
                'uuid' => $message->getUuid(),
                'text' => $message->getText(),
                'status' => $message->getStatus(),
            ];
        }

        return [
            'messages' => $messages,
            'page' => $page,
            'limit' => $limit,
            'totalItems' => $totalItems,
        ];

    }
}
