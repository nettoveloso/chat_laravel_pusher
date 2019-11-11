<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatMessage;


class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat');
    }

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $arrayMensage = [
            "who" => $user->id,
            "message" => $request->input('message')
        ];

        $message = $user->messages()->create([
            'message' => $request->input('message'),
            'user_id' => $user->id,
            'dialog' => json_encode($arrayMensage)
        ]);

        broadcast(new ChatMessage($user, $message))->toOthers();

        return ['status' => 'Message Sent!'];
    }

}
