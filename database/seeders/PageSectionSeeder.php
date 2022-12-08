<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PageSection;
use App\Models\Page;

class PageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PageSection::truncate();
        //
        $pages = [

            [
                'key' => 'home'
            ],
            [
                'key' => 'aboutus',
                "count" => 7,
            ],
            [
                'key' => 'contact'
            ],
            [
                'key' => 'partners'
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
                'key' => 'singleproduct'
            ],

        ];

        $in = [];
        $pageSections = [];
        foreach ($pages as $item) {
            $in[] = $item['key'];
            $pageSections[$item['key']] = $item;
        }

        //print_r($pageSections);
        //dd(3);

        $pages = Page::whereIn('key', $in)->get();



        $ins = [];
        $key = 0;
        foreach ($pages as $item) {

            if (isset($pageSections[$item->key]['count'])) {
                for ($i = 0; $i < $pageSections[$item->key]['count']; $i++) {
                    $ins[$key]['page_id'] = $item->id;
                    $key++;
                }
            } else {
                $ins[$key]['page_id'] = $item->id;
                $key++;
            }
        }
        //dd($ins);
        PageSection::insert($ins);
    }
}
