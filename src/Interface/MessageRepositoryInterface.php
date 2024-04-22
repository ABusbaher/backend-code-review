<?php
declare(strict_types=1);

namespace App\Interface;

use App\DTO\ListMessageDTO;

interface MessageRepositoryInterface
{
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
    public function getAllMessagesPaginated(ListMessageDTO $listMessageDTO): array;
}