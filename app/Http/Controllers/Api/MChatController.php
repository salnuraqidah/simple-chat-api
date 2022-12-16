<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use App\Models\HChat;
use App\Models\HGroup;
use App\Models\MChat;
use App\Models\ReadGroup;
use App\Models\StarChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MChatController extends Controller
{
    function store(Request $request, $param_id)
    {
        $user = auth()->user();
        if(isset($request['message']) && !empty($request['message'])) {
            $req1 = [
                "user1" => $user->id,
                "user2" => $param_id,
            ];
            $id = MChat::where('user1', $user->id)->Where('user2', $param_id)->orWhere("user1",$param_id)->Where('user2', $user->id)->orderBy('id', 'desc')->pluck("id")->first();
            if (!isset($id) && empty($id)) {
                $chat = MChat::create($req1);
                $id = $chat['id'];
            } else {
                HChat::where('m_chat_id',$id)->where('is_read',0)->where('user_from',$param_id)->update(['is_read'=>1]);
            }

            $req2 = [
                "m_chat_id" => $id,
                "message" => $request->message,
                "user_from" => $user->id,
                "user_to" => $param_id,
            ];

            $hchat = HChat::create($req2);
            $result = [
                "user1" => $hchat['user_from'],
                "user2" => $hchat['user_to'],
                "message" => $hchat['message'],
                "created_at" => date('Y-m-d H:i:s', strtotime($hchat->created_at))
            ];

            return response()->json(['status'=>true,'message' => 'message has been sent', 'data' => $result], 201);
        } else {
            return response()->json(['status'=>false,'message'=>'no messages sent', 'data' => null], 400);
        }
    }

    public function getListChat() {
        $user_id = auth()->user()->id;

        $lists = MChat::select('id','user1','user2')->with(['userFrom:id,name','userTo:id,name'])->where('user1', $user_id)->orWhere('user2', $user_id)->orderBy('id', 'desc')->get();
        $key = null;
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
                $chat = HChat::select('id','updated_at','message','user_from')->where('m_chat_id',$list->id)->where('is_read',0)->orderBy('id','desc');
                if ($chat->first()->user_from != $user_id) {
                    $count = $chat->count(); 
                } else {
                    $count = 0;
                }
                $data[$key]['chat_id'] = $id_kontak;
                $data[$key]['tipe'] = 'personal';
                $data[$key]['name'] = $kontak;
                $data[$key]['last_message'] = $chat->first()->message ?? null;
                $data[$key]['unread'] = $count;
                $data[$key]['last_message_at'] = date('Y-m-d H:i:s', strtotime($chat->first()->updated_at));
            }
            }

            $no = ($key) ? 0 : $key+1;
            $groups = GroupUser::with('group')->where('user_id',$user_id)->get();
            if (isset($groups[0])) {
                foreach ($groups as $key => $group) {
                    
                    $key += $no;
                    $last_read = ReadGroup::select('id','last_read')->where('m_group_id',$group->m_group_id)->where('user_id',$user_id)->orderBy('id','desc')->first();

                    if ($last_read) {
                        $q = HGroup::select('id')->where('m_group_id',$group->m_group_id)->where('updated_at', '>', $last_read->last_read);
                        $count = $q->count();

                    } else {
                        $q = HGroup::select('id','created_at')->where('m_group_id',$group->m_group_id)->whereNotIn('user_id', [$user_id])->orderBy('id','desc');
                        if ($q->count()){
                            $count = $q->count();
                        } else {
                            $count = 0;
                        }
                    }

                    $last_chat =HGroup::select('id','message','created_at')->where('m_group_id',$group->m_group_id)->orderBy('id','desc')->first();

                    if (isset($last_chat)) {
                        $tgl = $last_chat->created_at;
                        $msg = $last_chat->message;
                    } else {
                        $tgl = $group->group->created_at;
                        $msg = 'no message';
                    }

                    $data[$key]['chat_id'] = $group->m_group_id;
                    $data[$key]['tipe'] = 'group';
                    $data[$key]['name'] = $group->group->name;
                    $data[$key]['last_message'] = $msg;
                    $data[$key]['unread'] = $count;
                    $data[$key]['last_message_at'] = date('Y-m-d H:i:s', strtotime($tgl));
                }
            }
            if (!isset($lists[0]) && !isset($groups[0])) {
                return response()->json(['status'=>true,'message' => 'no message', 'data' => null], 200);                
            }
            return response()->json(['status'=>true,'message' => 'success', 'data' => $data], 200);
            
       

    }

    public function storeStarChat(Request $request) {
        $user_id = auth()->user()->id;
        if (isset($request['personal'][0])) {
            foreach ($request['personal'] as $key => $personal) {
                $q = HChat::select('message','user_from')->find($personal);
                $data[$key]['user_id'] = $user_id;
                $data[$key]['h_chat_id'] = $personal;

                $res[$key]['user_from'] = $q->user_from;
                $res[$key]['message'] = $q->message;
                $res[$key]['id'] = $personal;
            }
            DB::table('m_star_chat')->insert($data);
            return response()->json(['status'=>true,'message' => 'success', 'data' => $res], 200);
        } else if (isset($request['group'][0])) {
            foreach ($request['group'] as $key => $group) {
                $q = HGroup::select('message','user_id')->find($group);
                $data[$key]['user_id'] = $user_id;
                $data[$key]['h_group_id'] = $group;

                $res[$key]['user_from'] = $q->user_id;
                $res[$key]['message'] = $q->message;
                $res[$key]['id'] = $group;
            }
            DB::table('m_star_chat')->insert($data);
            return response()->json(['status'=>true,'message' => 'success', 'data' => $res], 200);

        } else {
            return response()->json(['status'=>false,'message' => 'no conversation selected', 'data' => null], 400);
        }

    }

    public function getStarChat() {
        $user_id = auth()->user()->id;

        $query = StarChat::with(['personal','group'])->where('user_id',$user_id)->get();
        $data = [];
        foreach ($query as $key => $value) {
            if (isset($value->h_chat_id) && !empty($value->h_chat_id)) {
                $chat = MChat::where('id',$value->personal->m_chat_id)->first();
                $id = $chat->user1 == $user_id ? $chat->user2 : $chat->user1; 
                $data[$key]['id_conversation'] = $value->h_chat_id;
                $data[$key]['id_chat'] = $id;
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
        return response()->json(['status'=>true,'message' => 'success', 'data' => $data], 200);

    }

    public function getConversation($param_id) {
        $user_id = auth()->user()->id;

        $id = MChat::where('user1', $user_id)->Where('user2', $param_id)->orWhere("user1",$param_id)->Where('user2', $user_id)->orderBy('id', 'desc')->pluck("id")->first();

        $chats = HChat::with(['userFrom'])->where('m_chat_id',$id)->orderBy('id','asc')->get();
        
        $data = [];
        if (isset($chats[0])) {
            foreach ($chats as $key => $chat) {
                $data[$key]['id'] = $chat->id;
                $data[$key]['user_id'] = $chat->userFrom->id;
                $data[$key]['user_name'] = $chat->userFrom->name; 
                $data[$key]['message'] = $chat->message;
                $data[$key]['created_at'] = date('Y-m-d H:i:s', strtotime($chat->created_at));
            }
            return response()->json(['status'=>true,'message' => 'success', 'data' => $data], 200);
        } else {
            return response()->json(['status'=>true,'message' => 'no conversation', 'data' => null], 200);
        }
    }
}
