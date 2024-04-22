<?php
declare(strict_types=1);

namespace App\Enum;

use App\Trait\EnumToArray;

enum StatusMessage: string
{
    use EnumToArray;

    case sent = 'sent';
    case read = 'read';
}
