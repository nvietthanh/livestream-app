<?php

namespace App\Http\Controllers;

use App\Events\SendHandShake;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CallController extends Controller
{
    public function listOnline()
    {
        $users = User::whereNot('id', auth()->user()->id)->get();

        return Inertia::render('Calling/Index', [
            'users' => $users
        ]);
    }

    public function handshake(Request $request)
    {
        SendHandShake::dispatch($request->senderId, $request->reciverId, $request->data);

        return response([
            "status" => true,
            "message" => "handshake send."
        ]);
    }
}
