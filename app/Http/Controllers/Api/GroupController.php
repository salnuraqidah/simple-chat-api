<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HGroup;
use App\Models\ReadGroup;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request, $id) {
        $user = auth()->user();
        if(isset($request['message']) && !empty($request['message'])) {
            $req = [
                "m_group_id" => $id,
                "user_id" => $user->id,
            ];
            $read = ReadGroup::create(array_merge($req,["last_read"=>date('Y-m-d H:i:s')]));
            $hgroup = HGroup::create(array_merge($req,["message" => $request->message]));
            $result = [
                "id" => $hgroup['id'],
                "m_group_id" => $hgroup['m_group_id'],
                "user_id" => $hgroup['user_id'],
                "message" => $hgroup['message'],
                "created_at" => date('Y-m-d H:i:s', strtotime($hgroup->created_at))
            ];

            return response()->json(['status'=>true,'message' => 'message has been sent','data' => $result], 201);
        } else {
            return response()->json(['status'=>false,'message'=>'no messages sent','data' => null], 400);
        }
    }

    public function getConversation($id) {
        $chats = HGroup::with(['user'])->where('m_group_id',$id)->orderBy('created_at','asc')->get();
        $data = [];
        if (isset($chats[0])) {
            foreach ($chats as $key => $chat) {
                $data[$key]['id'] = $chat->id;
                $data[$key]['user_id'] = $chat->user->id;
                $data[$key]['user_name'] = $chat->user->name; 
                $data[$key]['message'] = $chat->message;
                $data[$key]['created_at'] = date('Y-m-d H:i:s', strtotime($chat->created_at));
            }
            return response()->json(['status'=>true,'message' => 'success', 'data' => $data], 200);
        } else {
            return response()->json(['status'=>true,'message' => 'no conversation', 'data' => null], 200);
        }

    }
}
