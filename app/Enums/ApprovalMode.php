<?php

namespace App\Enums;

enum ApprovalMode: string
{
    case Any = 'any';
    case All = 'all';

    public function label(): string
    {
        return match ($this) {
            self::Any => 'Salah Satu',
            self::All => 'Semua',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Any => 'Cukup satu approver menyetujui',
            self::All => 'Semua approver harus menyetujui',
        };
    }
}
