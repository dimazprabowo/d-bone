<?php

namespace App\Enums;

/**
 * Status derived untuk alert jatuh tempo pembayaran Pengadaan.
 * Ambang "Proses" (kuning) diatur via System Configuration
 * `pengadaan.jatuh_tempo_warning_days` (lihat ConfigHelper::getJatuhTempoWarningDays()).
 */
enum JatuhTempoStatus: string
{
    case Aman = 'aman';
    case Proses = 'proses';
    case Lewat = 'lewat';

    public function label(): string
    {
        return match ($this) {
            self::Aman => 'Aman',
            self::Proses => 'Proses',
            self::Lewat => 'Lewat',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Aman => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            self::Proses => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400',
            self::Lewat => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        };
    }

    public function textClass(): string
    {
        return match ($this) {
            self::Aman => 'text-green-600 dark:text-green-400',
            self::Proses => 'text-amber-600 dark:text-amber-400',
            self::Lewat => 'text-red-600 dark:text-red-400',
        };
    }
}
