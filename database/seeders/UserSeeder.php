<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'test test',
            'email' => 'webmaster@gmail.com',
            'password' => '$2y$10$SakPBMAjmo0rhBU/0RuVjuTB4pOZqeU.pZVxpnOPR.PBe1nkdcsPa',
        ]);
    }
}
