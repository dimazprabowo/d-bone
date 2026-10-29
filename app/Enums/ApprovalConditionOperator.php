<?php

namespace App\Enums;

enum ApprovalConditionOperator: string
{
    case Eq = '=';
    case NotEq = '!=';
    case Lt = '<';
    case Lte = '<=';
    case Gt = '>';
    case Gte = '>=';
    case Between = 'between';
    case In = 'in';

    public function label(): string
    {
        return match ($this) {
            self::Eq => 'sama dengan',
            self::NotEq => 'tidak sama dengan',
            self::Lt => 'kurang dari',
            self::Lte => 'kurang dari / sama dengan',
            self::Gt => 'lebih dari',
            self::Gte => 'lebih dari / sama dengan',
            self::Between => 'di antara',
            self::In => 'salah satu dari',
        };
    }

    public function needsValue2(): bool
    {
        return $this === self::Between;
    }

    public static function forFieldType(string $type): array
    {
        return match ($type) {
            'number' => [self::Lt, self::Lte, self::Gt, self::Gte, self::Eq, self::Between],
            default => [self::Eq, self::NotEq],
        };
    }

    public function evaluate(mixed $actual, ?string $value, ?string $value2): bool
    {
        $actual = $actual instanceof \BackedEnum ? $actual->value : $actual;

        return match ($this) {
            self::Eq => $actual == $value,
            self::NotEq => $actual != $value,
            self::Lt => $actual < $value,
            self::Lte => $actual <= $value,
            self::Gt => $actual > $value,
            self::Gte => $actual >= $value,
            self::Between => $actual >= $value && $actual <= $value2,
            self::In => in_array($actual, array_map('trim', explode(',', (string) $value))),
        };
    }
}
