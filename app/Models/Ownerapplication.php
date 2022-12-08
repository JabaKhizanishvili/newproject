<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ownerapplication extends Model
{
    use HasFactory;
    protected $table = 'owneraplications';
    protected $fillable = [
        'name_of_ship',
        'ex_names',
        'imo_no',
        'type',
        'existing_flag',
        'call_sign',
        'grt',
        'nrt',
        'dwt',
        'date_keel_laid',
        'build',
        'framing_system',
        'builders',
        'loa',
        'lpp',
        'breath',
        'depth',
        'main_engine_builders',
        'type2',
        'bhp',
        'number_and_type_of_generators',
        'cargogear',
        'owning_company',
        'managing_company',
        'place_date',
        'name_of_applicant',
        'hull_machinery',
        'cargo_gear',
        'load_line',
        'safety_constructor',
        'safety_equipment',
        'passenger_safety',
        'safety_radio',
        'marpol_annexi',
        'marpol_annexii',
        'marpol_annexiii',
        'marpol_annexv',
        'marpol_annex_vi',
        'energy_efficiency',
        'engine_iopc',
        'fishing_vessel',
        'tonnage_certificate',
        'solid_bulk_cards',
        'dangerous_goods',
        'doc',
        'smc',
        'isps_on_board_verification',
        'mlc_2006',
        'ballast_water',
        'antifouling_survey',
        'exemption',
        'cargo_ship_safety_under_500_grt',
        'seaworthiness',
        'hsc_dsc_safety',
        'manual_approval',
    ];
}
