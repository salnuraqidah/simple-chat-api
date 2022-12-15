<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HGroup;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request, $id) {
        if(isset($request['message']) && !empty($request['message'])) {
            $req = [
                "m_group_id" => $id,
                "user_id" => $request->user_id,
                "message" => $request->message,
            ];
            $hgroup = HGroup::create($req);
            $result = [
                "id" => $hgroup['id'],
                "m_group_id" => $hgroup['m_group_id'],
                "user_id" => $hgroup['user_id'],
                "message" => $hgroup['message'],
                "created_at" => date('Y-m-d H:i:s', strtotime($hgroup->created_at))
            ];

            return response()->json(['msg' => 'success','header'=>201, 'data' => $result], 201);
        } else {
            return response()->json(['msg' => 'Tidak ada pesan yang disimpan', 'header'=>401, 'data' => null], 401);
        }
    }

    public function getConversation($id) {
        $chats = HGroup::with(['user'])->where('m_group_id',$id)->get();
        $data = [];
        foreach ($chats as $key => $chat) {
            $data[$key]['id'] = $chat->id;
            $data[$key]['user_id'] = $chat->user->id;
            $data[$key]['user_name'] = $chat->user->name; 
            $data[$key]['message'] = $chat->message;
            $data[$key]['created_at'] = date('Y-m-d H:i:s', strtotime($chat->created_at));
        }
        return response()->json(['msg' => 'success','header'=>200, 'data' => $data], 200);

    }
}
