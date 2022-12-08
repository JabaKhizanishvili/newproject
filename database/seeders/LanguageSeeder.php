<?php

/**
 *  database/seeders/LanguageSeeder.php
 *
 * Date-Time: 04.06.21
 * Time: 10:28
 * @author Insite LLC <hello@insite.international>
 */

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Class LanguageSeeder
 * @package Database\Seeders
 */
class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Language array
        $languages = [
            [
                'title' => 'ქართული',
                'locale' => 'ge',
                'status' => true,
                'default' => false
            ],
            [
                'title' => 'English',
                'locale' => 'en',
                'status' => true,
                'default' => true
            ],
        ];

        // Insert Languages
        Language::insert($languages);
    }
}
