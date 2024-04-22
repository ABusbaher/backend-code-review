<?php
declare(strict_types=1);

namespace App\Trait;

trait EnumToArray
{
    /**
     * @return string[] Returns an array of enum names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * @return string[] Returns an array of enum values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string> Returns an associative array of enum names to enum values
     */
    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }
}