<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Menu::create([
            "ge" => [
                'name' => 'ტესტ',
                'visible' => true,
            ],
            "en" => [
                'name' => 'test',
                'visible' => true,
            ],
            "partners" => "Bike",
            "partners_order" => null,
            "reports_order" => null,
            "subscribers_order" => null,
            "blog" => null,
            "blog_order" => null,
            "slider1" => "Bike",
            "layout" => "3",
            "status" => true,
            "show" => true,
        ]);
    }
}
