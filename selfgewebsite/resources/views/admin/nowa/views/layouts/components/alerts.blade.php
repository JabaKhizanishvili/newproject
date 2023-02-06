
<!--Internal  Perfect-scrollbar js -->
<script src="{{asset('admin/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>

<!-- Internal Treeview js -->
<script src="{{asset('admin/assets/plugins/treeview/treeview.js')}}"></script>

<!--Internal  Notify js -->
<script src="{{asset('admin/assets/plugins/notify/js/notifIt.js')}}"></script>
<script src="{{asset('admin/assets/plugins/notify/js/notifit-custom.js')}}"></script>

{{--@dd(session()->all())--}}

    @if(session('warning'))

        <script>

            notif({
                type: "warning",
                msg: "<b>Warning: </b>{{session('warning')}}",
                position: "center",
                autohide: false
            });

        </script>
    @endif

    @if(session('success'))

        <script>

            notif({
                type: "success",
                msg: "<b>Success: </b>{{session('success')}}",
                position: "center",
                autohide: false
            });

        </script>
    @endif

    @if(session('danger'))


        <script>

            notif({
                type: "error",
                msg: "<b>Danger: </b>{{session('danger')}}",
                position: "center",
                autohide: false
            });

        </script>
    @endif




