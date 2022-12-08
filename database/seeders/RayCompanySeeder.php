<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RayCompany;

class RayCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $irons = [
            [
                "name" => "rayproduction",
                "slug" => "rayproduction"
            ],
            [
                "name" => "rayanimation",
                "slug" => "rayanimation"
            ],
            [
                "name" => "raycrypto",
                "slug" => "raycrypto"
            ],
        ];
        foreach ($irons as $iron) {
            RayCompany::create($iron);
        }
    }
}
