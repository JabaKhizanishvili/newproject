<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Settings array
        $Settings = [
            [
                'key' => 'phone'
            ],
            [
                'key' => 'address'
            ],
            [
                'key' => 'email'
            ],
            [
                'key' => 'facebook'
            ],
            [
                'key' => 'instagram'
            ],
            [
                'key' => 'linkedin'
            ],
            [
                'key' => 'twitter'
            ],
        ];

        // Insert Settings
        Setting::insert($Settings);
    }
}
