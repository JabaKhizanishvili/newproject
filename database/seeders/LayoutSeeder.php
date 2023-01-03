<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layout;

class LayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $layouts = [
            [
                "name" => "category",
                "parent_id" => null,
                "group" => true,
            ],
            [
                "name" => "blog",
                "parent_id" => 1,
            ],
            [
                "name" => "main",
                "parent_id" => 1,
            ],
            [
                "name" => "vaccancy",
                "parent_id" => 1,
            ],
            [
                "name" => "post",
                "parent_id" => null,
            ],
            [
                "name" => "contact",
                "parent_id" => null,
            ],
            [
                "name" => "application",
                "parent_id" => null,
            ],
            [
                "name" => "singlepost",
                "parent_id" => null,
            ],
        ];

        // // Insert Languages
        foreach ($layouts as $val) {
            Layout::create($val);
        }
    }
}
