<?php

namespace App\Http\Controllers;

use App\Events\StreamAnswer;
use App\Events\StreamOffer;
use Illuminate\Http\Request;

class WebrtcStreamingController extends Controller
{
    public function makeStreamOffer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['receiver'] = $request->receiver;
        $data['offer'] = $request->offer;

        event(new StreamOffer($data));
    }

    public function makeStreamAnswer(Request $request)
    {
        $data['broadcaster'] = $request->broadcaster;
        $data['answer'] = $request->answer;
        event(new StreamAnswer($data));
    }
}
