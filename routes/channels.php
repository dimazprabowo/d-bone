<?php

use App\Models\Pengadaan;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    return $user->chats()->where('chats.id', $chatId)->exists();
});

Broadcast::channel('pengadaan.{id}', function ($user, $id) {
    $pengadaan = Pengadaan::find($id);

    return $pengadaan && $user->can('view', $pengadaan);
});
