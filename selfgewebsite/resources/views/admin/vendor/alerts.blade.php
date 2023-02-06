@if(session('warning'))
    <div class="col s12">
        <div class="card-alert card gradient-45deg-amber-amber">
            <div class="card-content white-text">
                <p>
                    <i class="material-icons">warning</i>{{session('warning')}}</p>
            </div>
            <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="col s12">
        <div class="card-alert card gradient-45deg-green-teal">
            <div class="card-content white-text">
                <p>
                    <i class="material-icons">check</i> SUCCESS : {{session('success')}}</p>
            </div>
            <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
@endif

@if(session('danger'))
    <div class="card-alert card gradient-45deg-red-pink">
        <div class="card-content white-text">
            <p>
                <i class="material-icons">error</i> DANGER : {{session('danger')}}</p>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>

    </div>
@endif
{{-- page script --}}
@section('page-script')
    <script src="{{asset('js/scripts/ui-alerts.js')}}"></script>
@endsection