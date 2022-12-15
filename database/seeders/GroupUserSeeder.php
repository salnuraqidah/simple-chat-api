<?php

namespace Database\Seeders;

use App\Models\MGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group_id = MGroup::pluck("id")->first();
        $users = User::select("id")->get();
        $data = [];
        foreach ($users as $key => $user) {
            $data[$key]['m_group_id'] = $group_id;
            $data[$key]['user_id'] = $user->id;
            $data[$key]['created_at'] = date('Y-m-d H:i:s');
            $data[$key]['updated_at'] = date('Y-m-d H:i:s');
        }

        DB::table('group_users')->insert($data);

    }
}
