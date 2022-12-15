<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HChat;
use App\Models\MChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MChatController extends Controller
{
    function store(Request $request)
    {
        if(isset($request['message']) && !empty($request['message'])) {
            $req1 = [
                "user1" => $request->user1,
                "user2" => $request->user2,
            ];
            $id = MChat::where('user1', $request->user1)->Where('user2', $request->user2)->orWhere("user1",$request->user2)->Where('user2', $request->user1)->orderBy('id', 'desc')->pluck("id")->first();
            if (!isset($id) && empty($id)) {
                $chat = MChat::create($req1);
                $id = $chat['id'];
            }

            $req2 = [
                "m_chat_id" => $id,
                "message" => $request->message,
                "user_from" => $request->user1,
                "user_to" => $request->user2
            ];

            $hchat = HChat::create($req2);
            $result = [
                "user1" => $hchat['user_to'],
                "user2" => $hchat['user_from'],
                "message" => $hchat['message'],
                "created_at" => date('Y-m-d H:i:s', strtotime($hchat->created_at))
            ];

            return response()->json(['msg' => 'Data created', 'data' => $result], 201);
        } else {
            return response()->json(['msg' => 'Tidak ada pesan yang disimpan', 'data' => null], 401);
        }
    }
}
