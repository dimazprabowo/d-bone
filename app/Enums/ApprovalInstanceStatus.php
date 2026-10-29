<?php

namespace App\Enums;

enum ApprovalInstanceStatus: string
{
    case InProgress = 'in_progress';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'Dalam Proses',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::InProgress => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
            self::Approved => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
            self::Rejected => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            self::Cancelled => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
        };
    }
}
