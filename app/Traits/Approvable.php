<?php

namespace App\Traits;

use App\Models\ApprovalInstance;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Approvable
{
    /**
     * Identifier entity untuk master alur (e.g. 'pengadaan').
     */
    abstract public static function approvalEntityType(): string;

    /**
     * Hook saat instance approval selesai — entity mengupdate statusnya sendiri.
     */
    abstract public function onApprovalFinalized(ApprovalInstance $instance): void;

    public function approvalInstances(): MorphMany
    {
        return $this->morphMany(ApprovalInstance::class, 'approvable');
    }

    public function latestApprovalInstance(): MorphOne
    {
        return $this->morphOne(ApprovalInstance::class, 'approvable')->latestOfMany();
    }
}
