<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwneraplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owneraplications', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_ship');
            $table->string('ex_names');
            $table->string('imo_no');
            $table->string('type');
            $table->string('existing_flag');
            $table->string('call_sign');
            $table->string('grt');
            $table->string('nrt');
            $table->string('dwt');
            $table->string('date_keel_laid');
            $table->string('build');
            $table->string('framing_system');
            $table->string('builders');
            $table->string('loa');
            $table->string('lpp');
            $table->string('breath');
            $table->string('depth');
            $table->string('main_engine_builders');
            $table->string('type2');
            $table->string('bhp');
            $table->string('number_and_type_of_generators');
            $table->string('cargogear');
            $table->string('owning_company');
            $table->string('managing_company');
            $table->string('place_date');
            $table->string('name_of_applicant');
            $table->string('hull_machinery');
            $table->string('cargo_gear');
            $table->string('load_line');
            $table->string('safety_constructor');
            $table->string('safety_equipment');
            $table->string('passenger_safety');
            $table->string('safety_radio');
            $table->string('marpol_annexi');
            $table->string('marpol_annexii');
            $table->string('marpol_annexiii');
            $table->string('marpol_annexv');
            $table->string('marpol_annex_vi');
            $table->string('energy_efficiency');
            $table->string('engine_iopc');
            $table->string('fishing_vessel');
            $table->string('tonnage_certificate');
            $table->string('solid_bulk_cards');
            $table->string('dangerous_goods');
            $table->string('doc');
            $table->string('smc');
            $table->string('isps_on_board_verification');
            $table->string('mlc_2006');
            $table->string('ballast_water');
            $table->string('antifouling_survey');
            $table->string('exemption');
            $table->string('cargo_ship_safety_under_500_grt');
            $table->string('seaworthiness');
            $table->string('hsc_dsc_safety');
            $table->string('manual_approval');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('owneraplication');
    }
}
