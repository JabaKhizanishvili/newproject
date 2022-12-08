<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Page::truncate();
        // Pages array
        $pages = [
            [
                'key' => 'home'
            ],
            [
                'key' => 'aboutus',
            ],
            [
                'key' => 'contact'
            ],
            [
                'key' => 'news'
            ],
            [
                'key' => 'rayacademy'
            ],
            [
                'key' => 'rayanimation'
            ],
            [
                'key' => 'rayproduction'
            ],
            [
                'key' => 'raycrypto'
            ],
            [
                'key' => 'projects'
            ],
            [
                'key' => 'singleproduct'
            ],
        ];

        // Insert Pages
        Page::insert($pages);
    }
}
