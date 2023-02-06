<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // categorytranslations
        $Category = [

            [
                "slug" => "brand",
                "ge" => ["name" => "BRAND PLATFORM & IDENTITY DESIGN"],
                "en" => ["name" => "BRAND PLATFORM & IDENTITY DESIGN"],
            ],
            [
                "slug" => "creative",
                "ge" => ["name" => "CREATIVE"],
                "en" => ["name" => "CREATIVE"],
            ],
            [
                "slug" => "video",
                "ge" => ["name" => "Video & Graphic Production"],
                "en" => ["name" => "Video & Graphic Production"],
            ],
            [
                "slug" => "socmedia",
                "ge" => ["name" => "SOCIAL MEDIA"],
                "en" => ["name" => "SOCIAL MEDIA"],
            ],
            [
                "slug" => "art",
                "ge" => ["name" => "ART DIRECTION"],
                "en" => ["name" => "ART DIRECTION"],
            ]
        ];

        // // Insert Languages
        foreach ($Category as $cat) {
            Category::create($cat);
        }

        // Category::create([
        //     "name" => "test"
        // ]);
    }
}
