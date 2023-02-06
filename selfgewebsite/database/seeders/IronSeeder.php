<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Iron;

class IronSeeder extends Seeder
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
                "slug" => "პროფილი მილკვადრატი",
                "ge" => ["name" => "პროფილი მილკვადრატი"],
                "en" => ["name" => "profili milkvadrati"],
            ],
            [
                "slug" => "მილი",
                "ge" => ["name" => "მილი"],
                "en" => ["name" => "mili"],
            ],
            [
                "slug" => "ლითონის ფურცელი",
                "ge" => ["name" => "ლითონის ფურცელი"],
                "en" => ["name" => "litonis furceli"],
            ],
            [
                "slug" => "ლითონის ფურცელი დაჟატული",
                "ge" => ["name" => "ლითონის ფურცელი დაჟატული"],
                "en" => ["name" => "litonis furceli dajatuli"],
            ],
            [
                "slug" => "ლითონის ფურცელი დახვრეტილი",
                "ge" => ["name" => "ლითონის ფურცელი დახვრეტილი"],
                "en" => ["name" => "litonis furceli daxvretili"],
            ],
            [
                "slug" => "ორტისებრი",
                "ge" => ["name" => "ორტისებრი"],
                "en" => ["name" => "ortisebri"],
            ],
            [
                "slug" => "შველერი",
                "ge" => ["name" => "შველერი"],
                "en" => ["name" => "shveleri"],
            ],
            [
                "slug" => "კუთხოვანა",
                "ge" => ["name" => "კუთხოვანა"],
                "en" => ["name" => "kutxovana"],
            ],
            [
                "slug" => "ზოლოვანა",
                "ge" => ["name" => "ზოლოვანა"],
                "en" => ["name" => "shveleri"],
            ],
            [
                "slug" => "კვადრატი",
                "ge" => ["name" => "კვადრატი"],
                "en" => ["name" => "kvadrati"],
            ],
            [
                "slug" => "გრანულა",
                "ge" => ["name" => "გრანულა"],
                "en" => ["name" => "granula"],
            ],
            [
                "slug" => "არმატურა",
                "ge" => ["name" => "არმატურა"],
                "en" => ["name" => "armatura"],
            ],
        ];

        // // Insert Languages
        foreach ($irons as $iron) {
            Iron::create($iron);
        }
    }
}
