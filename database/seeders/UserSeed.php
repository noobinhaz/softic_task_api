<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $accountInfo = [
            [
                'id' => 1, 
                'name' => 'Super Admin', 
                'email' => 'admin@example.com',
                'password' => bcrypt('12345678'),
                'role' => 1
            ],
            [
                'id' => 2, 
                'name' => 'User1', 
                'email' => 'userBeAffiliate@example.com',
                'password' => bcrypt('12345678'),
                'role' => 2
            ]
        ];

        User::truncate()->insert($accountInfo);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
