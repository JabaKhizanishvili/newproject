{{-- @php
    print_r($data)
@endphp --}}

@extends('admin.nowa.views.layouts.app')

@section('styles')


@endsection

@section('content')



   <div class="container-fluid">

    <div class="row d-flex justify-content-around">
        <div class="col-md-5">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap">
                        <thead>
                            <tr>
                                {{-- <th>Name</th>
                                <th>value</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">name of ship</th>
                                <td>{{$data->name_of_ship}}</td>
                            </tr>
                            <tr>
                                <th scope="row">ex names</th>
                                <td>{{$data->ex_names}}</td>

                            </tr>
                            <tr>
                                <th scope="row">imo no</th>
                                <td>{{$data->imo_no}}</td>

                            </tr>
                            <tr>
                                <th scope="row">type</th>
                                <td>{{$data->type}}</td>

                            </tr>
                            <tr>
                                <th scope="row">Existing Flag</th>
                                <td>{{$data->existing_flag}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Call Sign</th>
                                <td>{{$data->call_sign}}</td>
                            </tr>
                            <tr>
                                <th scope="row">GRT</th>
                                <td>{{$data->grt}}</td>
                            </tr>
                            <tr>
                                <th scope="row">NRT</th>
                                <td>{{$data->nrt}}</td>
                            </tr>
                            <tr>
                                <th scope="row">DWT</th>
                                <td>{{$data->dwt}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Date Keel Laid</th>
                                <td>{{$data->date_keel_laid}}</td>
                            </tr>
                            <tr>
                                <th scope="row">build</th>
                                <td>{{$data->build}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Framing System</th>
                                <td>{{$data->framing_system}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Builders</th>
                                <td>{{$data->builders}}</td>
                            </tr>
                            <tr>
                                <th scope="row">LOA</th>
                                <td>{{$data->loa}}</td>
                            </tr>
                            <tr>
                                <th scope="row">LPP</th>
                                <td>{{$data->lpp}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Breath</th>
                                <td>{{$data->breath}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Depth</th>
                                <td>{{$data->depth}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Main Engine Builders</th>
                                <td>{{$data->main_engine_builders}}</td>
                            </tr>
                            <tr>
                                <th scope="row">type</th>
                                <td>{{$data->type}}</td>
                            </tr>
                            <tr>
                                <th scope="row">BHP</th>
                                <td>{{$data->bhp}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Number and type of generators</th>
                                <td>{{$data->number_and_type_of_generators}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Cargo Gear</th>
                                <td>{{$data->cargogear}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Owning Company</th>
                                <td>{{$data->owning_company}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Managing Company</th>
                                <td>{{$data->managing_company}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Place and Date of Survays</th>
                                <td>{{$data->place_date}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Name of Applicant</th>
                                <td>{{$data->name_of_applicant}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Hull & Machinery</th>
                                <td>{{$data->hull_machinery}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap">
                        <thead>
                            {{-- <tr>
                                <th>Name</th>
                                <th>value</th>
                            </tr> --}}
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Cargo Gear</th>
                                <td>{{$data->cargo_gear}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Load Line</th>
                                <td>{{$data->load_line}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Safety Constructor</th>
                                <td>{{$data->safety_constructor}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Safety Equipment</th>
                                <td>{{$data->safety_equipment}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Passenger Safety</th>
                                <td>{{$data->passenger_safety}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Safety Radio</th>
                                <td>{{$data->safety_radio}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MARPOL ANNEX I</th>
                                <td>{{$data->marpol_annexi}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MARPOL ANNEX II</th>
                                <td>{{$data->marpol_annexii}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MARPOL ANNEX III</th>
                                <td>{{$data->marpol_annexiii}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MARPOL ANNEX V </th>
                                <td>{{$data->marpol_annexv}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MARPOL ANNEX VI</th>
                                <td>{{$data->marpol_annex_vi}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Energy Efficiency</th>
                                <td>{{$data->energy_efficiency}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Engine IAPPC</th>
                                <td>{{$data->engine_iopc}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Fishing Vessel</th>
                                <td>{{$data->fishing_vessel}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Tonnage Certificate</th>
                                <td>{{$data->tonnage_certificate}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Solid Bulk Cargoes</th>
                                <td>{{$data->solid_bulk_cards}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Dangerous Goods</th>
                                <td>{{$data->dangerous_goods}}</td>
                            </tr>
                            <tr>
                                <th scope="row">DOC</th>
                                <td>{{$data->doc}}</td>
                            </tr>
                            <tr>
                                <th scope="row">SMS</th>
                                <td>{{$data->sms}}</td>
                            </tr>
                            <tr>
                                <th scope="row">ISPS On board Verification</th>
                                <td>{{$data->isps_on_board_verification}}</td>
                            </tr>
                            <tr>
                                <th scope="row">MLC 2006</th>
                                <td>{{$data->mlc_2006}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Ballast Water</th>
                                <td>{{$data->ballast_water}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Antifouling Survey</th>
                                <td>{{$data->antifouling_survey}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Exemption</th>
                                <td>{{$data->exemption}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Cargo Ship Safety Under 500 GRT</th>
                                <td>{{$data->cargo_ship_safety_under_500_grt}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Seawothiness</th>
                                <td>{{$data->seaworthiness}}</td>
                            </tr>
                            <tr>
                                <th scope="row">HSC/DSC Safety</th>
                                <td>{{$data->hsc_dsc_safety}}</td>
                            </tr>



                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>



    </div>



   </div>


@endsection

@section('scripts')



@endsection
