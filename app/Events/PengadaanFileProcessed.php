<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PengadaanFileProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $pengadaanId,
        public string $entityType,
        public string $status,
        public ?int $recordId = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pengadaan.'.$this->pengadaanId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'entity' => $this->entityType,
            'status' => $this->status,
            'record_id' => $this->recordId,
        ];
    }
}
