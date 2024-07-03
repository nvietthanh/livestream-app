<?php

namespace App\Http\Controllers;

use App\Events\RoomStreamingAnswerEvent;
use App\Events\RoomStreamingOfferEvent;
use App\Events\SendMessageStreamEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiveStreamController extends Controller
{
    public function store()
    {

    }

    public function liveStream()
    {
        return Inertia::render('Streaming/Livestream');
    }

    public function streaming()
    {
        return Inertia::render('Streaming/Streaming');
    }

    public function streamOffer(Request $request)
    {
        RoomStreamingOfferEvent::dispatch($request->roomId, $request->userId, $request->data);

        return response([
            "status" => true,
            "message" => "handshake send."
        ]);
    }

    public function streamAnswer(Request $request)
    {
        RoomStreamingAnswerEvent::dispatch($request->roomId, $request->userId, $request->data);

        return response([
            "status" => true,
            "message" => "handshake send."
        ]);
    }

    public function sendMessage(Request $request)
    {
        $user = auth()->user();
        SendMessageStreamEvent::dispatch($request->roomId, $user, $request->content);

        return response([
            "status" => true,
            "message" => "send message."
        ]);
    }
}
