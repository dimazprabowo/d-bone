<?php

namespace App\Enums;

enum ApprovalStepStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Active => 'Sedang Diproses',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Skipped => 'Dilewati',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            self::Active => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            self::Approved => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
            self::Rejected => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            self::Skipped => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
        };
    }
}
