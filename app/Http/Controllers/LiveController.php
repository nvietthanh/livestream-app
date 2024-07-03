<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Events\StreamAnswer;
use App\Events\StreamOffer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiveController extends Controller
{
    public function liveStream()
    {
        return Inertia::render('Livestream');
    }

    public function streaming(string $id)
    {
        return Inertia::render('Streaming');
    }

    public function makeStreamOffer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['receiver'] = $request->receiver;
        $data['offer'] = $request->offer;
        StreamOffer::dispatch($data);

        return response()->json(['data' => $data]);
    }

    public function makeStreamAnswer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['answer'] = $request->answer;
        StreamAnswer::dispatch($data);

        return response()->json(['data' => $data]);
    }

    public function sendMessage(string $id, Request $request)
    {
        $currentUser = auth()->user();

        $data = [
            'id' => rand(1, 100000),
            'content' => $request->content,
            'user' => [
                'id' =>  $currentUser->id,
                'name' =>  $currentUser->name,
            ]
        ];
        SendMessageEvent::dispatch($id, $data);

        return response()->json(['data' => $data]);
    }
}
