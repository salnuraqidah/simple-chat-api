<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use App\Models\HChat;
use App\Models\MChat;
use App\Models\StarChat;
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
            } else {
                HChat::where('m_chat_id',$id)->where('is_read',0)->where('user_from',$request->user2)->update(['is_read'=>1]);
            }

            $req2 = [
                "m_chat_id" => $id,
                "message" => $request->message,
                "user_from" => $request->user1,
                "user_to" => $request->user2,
            ];

            $hchat = HChat::create($req2);
            $result = [
                "user1" => $hchat['user_from'],
                "user2" => $hchat['user_to'],
                "message" => $hchat['message'],
                "created_at" => date('Y-m-d H:i:s', strtotime($hchat->created_at))
            ];

            return response()->json(['msg' => 'Data created', 'data' => $result], 201);
        } else {
            return response()->json(['msg' => 'Tidak ada pesan yang disimpan', 'data' => null], 401);
        }
    }

    public function getListChat($user_id) {
        $lists = MChat::select('id','user1','user2')->with(['userFrom:id,name','userTo:id,name'])->where('user1', $user_id)->orWhere('user2', $user_id)->orderBy('id', 'desc')->get();
        if (isset($lists[0])) {
            $data = array();
            foreach ($lists as $key => $list) {
                if ($list->userFrom->id == $user_id) {
                    $kontak = $list->userTo->name;
                    $id_kontak = $list->userTo->id;
                } else {
                    $kontak = $list->userFrom->name;
                    $id_kontak = $list->userFrom->id;
                }
                $chat = HChat::select('id')->where('m_chat_id',$list->id)->where('user_to',$user_id)->where('is_read',0)->count();
                $data['personal'][$key]['chat_id'] = $list->id;
                $data['personal'][$key]['id_kontak'] = $id_kontak;
                $data['personal'][$key]['kontak'] = $kontak;
                $data['personal'][$key]['unread'] = $chat;
            }
            $groups = GroupUser::with('group')->where('user_id',$user_id)->get();
            if (isset($groups[0])) {
                foreach ($groups as $key => $group) {
                    $data['group'][$key]['m_group_id'] = $group->m_group_id;
                    $data['group'][$key]['group_name'] = $group->group->name;
                }
            }
            return response()->json(['msg' => 'success', 'data' => $data], 200);
            
        }

    }

    public function storeStarChat(Request $request) {
        if (isset($request['personal'][0])) {
            foreach ($request['personal'] as $key => $personal) {
                $data[$key]['user_id'] = $request->user_id;
                $data[$key]['h_chat_id'] = $personal;
            }
        }
        if (isset($request['group'][0])) {
            foreach ($request['group'] as $key => $group) {
                $data[$key]['user_id'] = $request->user_id;
                $data[$key]['h_group_id'] = $group;
            }
        }
        DB::table('m_star_chat')->insert($data);
    }

    public function getStarChat($user_id) {
        $query = StarChat::with(['personal','group'])->where('user_id',$user_id)->get();
        foreach ($query as $key => $value) {
            if (isset($value->h_chat_id) && !empty($value->h_chat_id)) {
                $data[$key]['id_conversation'] = $value->h_chat_id;
                $data[$key]['id_chat'] = $value->personal->m_chat_id;
                $data[$key]['tipe'] = 'personal';
                $data[$key]['message'] = $value->personal->message;
                $data[$key]["created_at"] = date('Y-m-d H:i:s', strtotime($value->personal->created_at));
            } else {
                $data[$key]['id_conversation'] = $value->h_group_id;
                $data[$key]['id_chat'] = $value->group->m_group_id;
                $data[$key]['tipe'] = 'group';
                $data[$key]['message'] = $value->group->message;
                $data[$key]["created_at"] = date('Y-m-d H:i:s', strtotime($value->group->created_at));
            }
        }
        return response()->json(['msg' => 'success', 'data' => $data], 200);

    }
}
