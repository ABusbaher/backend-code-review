<?php
declare(strict_types=1);

namespace App\DTO;

use App\Enum\StatusMessage;
use App\Validator\IsNumeric;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

readonly class ListMessageDTO
{
    public function __construct(
        #[IsNumeric]
        #[NotBlank(
            message: 'You must provide limit'
        )]
        #[Range(
            notInRangeMessage: 'Messages per page should be be between {{ min }} and {{ max }}, got {{ value }}',
            min: 1,
            max: 50,
        )]
        private int|string $limit,
        #[IsNumeric]
        #[Positive]
        private int|string $page = '1',
        #[Type(type:'string')]
        #[Choice(
            callback: [StatusMessage::class, 'array'],
            message: 'Invalid status.'
        )]
        private ?string $status = null
    ) {}

    public function getLimit(): int|string
    {
        return $this->limit;
    }

    public function getPage(): int|string
    {
        return $this->page;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }


}