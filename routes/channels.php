<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('client-channel.{id}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// // Signaling Offer and Answer Channels
// Broadcast::channel('client-ư.{userId}', function ($user, $userId) {
//     return (int) $user->id === (int) $userId;
// });

# chat room metting
Broadcast::channel('online-users', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('handshake.{id}', function ($user) {
    return true;
});

# livestrea
Broadcast::channel('room_livestream_offer.{id}', function ($user, $id) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('room_livestream_answer.{id}', function ($user, $id) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('streaming-room.{id}', function ($user, $id) {
    return ['id' => $user->id, 'name' => $user->name];
});
