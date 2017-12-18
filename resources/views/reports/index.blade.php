@extends('layouts.app')

@section('inline_styles')
    <style>
        .select2-container--default {
            width: 250px !important;
        }
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 500px;
            min-height: 400px;
            overflow-y: auto;
        }

        .hide {
            display: none !important;
        }

        #footer {
            position:fixed;
            left:0px;
            bottom:0px;
            height:30px;
            width:100%;
            background:#999;
        }
    </style>
@endsection

@section('styles')
    <!-- DataTables -->
    <link href="/vendor/ubold/assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    {{--<link href="/vendor/ubold/assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>--}}
    <link href="/vendor/ubold/assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="/vendor/ubold/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link href="/vendor/ubold/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <h4 class="page-title">Reports</h4>
            <ol class="breadcrumb">
                <li class="active">
                    Reports
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <form class="form-inline" role="form" id="search-form">

                        @if (Sentinel::getUser()->isAdmin())
                                <div class="form-group m-l-10">
                                    <label class="sr-only" for="">Phòng ban</label>
                                    {!! Form::select('department_id', ['' => '--- Chọn phòng ban ---'] + Helpers::departmentList(), null, ['class' => 'form-control select2']) !!}
                                </div>
                            @endif

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Tên Advertiser</label>
                                {!! Form::select('user_id', ['' => '--- Chọn Advertiser ---'] + Helpers::getListUserInGroup(), null, ['class' => 'form-control']) !!}
                            </div>

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Theo ngày</label>
                                <input class="form-control input-daterange-datepicker" type="text" name="date" value="{{ \Carbon\Carbon::today()->format('d/m/Y') }} - {{ \Carbon\Carbon::today()->format('d/m/Y') }}" placeholder="Theo ngày" style="width: 200px;"/>
                            </div>


                            @if (!Sentinel::getUser()->isAdmin() && !Sentinel::getUser()->isManager())

                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Theo tài khoản</label>
                                {!! Form::select('content_id', ['' => '--- Chọn Tài khoản ---'] + Helpers::getAdvertiserList(), ['class' => 'form-control']) !!}
                            </div>

                            @endif


                            <div class="form-group m-l-10">
                                <label class="sr-only" for="">Loại</label>
                                {!! Form::select('type', ['' => '--- Chọn Loại ---'] + config('system.insight.values'), config('system.insight.types.campaign'), ['class' => 'form-control']) !!}
                            </div>

                            <button type="submit" value="search" name="search" class="btn btn-success waves-effect waves-light m-l-15">Tìm kiếm</button>
                        </form>

                            <div class="form-group pull-right">
                                {!! Form::open(['route' => 'reports.export', 'method' => 'get', 'role' => 'form', 'class' => 'form-inline', 'id' => 'export-form']) !!}

                                {{Form::hidden('filter_department_id', null)}}
                                {{Form::hidden('filter_user_id', null)}}
                                {{Form::hidden('filter_date', null)}}
                                {{Form::hidden('filter_type', null)}}

                                <button class="btn btn-danger waves-effect waves-light m-t-15" value="export" type="submit" name="export">
                                    <i class="fa fa-download"></i>&nbsp; Xuất Excel
                                </button>
                                {!! Form::close() !!}

                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title"><b>Report By Date</b></h4>
                <p class="text-muted font-13 m-b-30"></p>
                <table id="dataTables-reports" class="table table-striped table-bordered table-actions-bar">
                    <thead>
                    <tr>
                        <th style="width: 10px !important;"><input name="select_all" value="1" type="checkbox" style="display: none"></th>
                        <th width="10%">Date</th>
                        <th width="10%">SocialID</th>
                        <th width="30%">SocialName</th>
                        <th width="10%">Level</th>
                        <th width="10%">Type</th>
                        <th width="10%">Result</th>
                        <th width="10%">CostPerResult</th>
                        <th width="10%">Spend</th>
                    </tr>
                    </thead>
                    <tfoot align="right" id="">
                    <tr>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th width="10%" id="result_total"></th>
                        <th width="10%" id="cpr_total"></th>
                        <th width="10%" id="spent_total"></th>
                    </tr>
                    </tfoot>

                </table>

            </div>
            {{--<div style="width: 100%; position: fixed; bottom: 0px; background-color: white; padding: 10px 0 10px 50px;">--}}
                {{--<table>--}}
                    {{--<tr>--}}
                        {{--<th style="width: 10px !important;"><input name="select_all" value="1" type="checkbox" style="display: none"></th>--}}
                        {{--<th width="10%"></th>--}}
                        {{--<th width="10%"></th>--}}
                        {{--<th width="30%"></th>--}}
                        {{--<th width="10%"></th>--}}
                        {{--<th width="10%"></th>--}}
                        {{--<th width="10%" id="result_total"></th>--}}
                        {{--<th width="10%" id="cpr_total"></th>--}}
                        {{--<th width="10%" id="spent_total"></th>--}}
                    {{--</tr>--}}
                {{--</table>--}}
            {{--</div>--}}

        </div>

    </div>

@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.bootstrap.js"></script>

    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.bootstrap.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/jszip.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/pdfmake.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/vfs_fonts.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.html5.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/buttons.print.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.keyTable.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/responsive.bootstrap.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.scroller.min.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.colVis.js"></script>
    <script src="/vendor/ubold/assets/plugins/datatables/dataTables.fixedColumns.min.js"></script>
    <script type="text/javascript" src="/js/jquery.number.min.js"></script>

    <script src="/vendor/ubold/assets/pages/datatables.init.js"></script>
    <script src="/vendor/ubold/assets/plugins/select2/js/select2.full.min.js"></script>
    <script src="/js/handlebars.js"></script>

    <script src="/vendor/ubold/assets/plugins/moment/moment.js"></script>
    <script src="/vendor/ubold/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
@endsection

@section('inline_scripts')
    <script type="text/javascript">
        var resultTotal = 0;
        var cprTotal = 0;
        var spendTotal = 0;

        var resultTotalSelected = 0;
        var cprTotalSelected = 0;
        var spendTotalSelected = 0;

        var rows_selected = [];

        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        function updateTotalNumber(type, data) {
            if (type == 1) {
                resultTotalSelected += intVal(data.result);
                cprTotalSelected += intVal(data.cost_per_result);
                spendTotalSelected += intVal(data.spend);
            } else {
                resultTotalSelected -= intVal(data.result);
                cprTotalSelected -= intVal(data.cost_per_result);
                spendTotalSelected -= intVal(data.spend);
            }

            if (rows_selected.length == 0) {
                $('div.dataTables_scrollFoot #result_total').html($.number(resultTotal));
                $('div.dataTables_scrollFoot #cpr_total').html($.number(cprTotal));
                $('div.dataTables_scrollFoot #spent_total').html($.number(spendTotal));
            } else {
                $('div.dataTables_scrollFoot #result_total').html(resultTotalSelected);
                $('div.dataTables_scrollFoot #cpr_total').html($.number(cprTotalSelected));
                $('div.dataTables_scrollFoot #spent_total').html($.number(spendTotalSelected));
            }
        }

        function updateDataTableSelectAllCtrl(table){
            var $table             = table.table().node();
            var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
            var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
            var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

            // If none of the checkboxes are checked
            if($chkbox_checked.length === 0){
                chkbox_select_all.checked = false;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

                // If all of the checkboxes are checked
            } else if ($chkbox_checked.length === $chkbox_all.length){
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = false;
                }

                // If some of the checkboxes are checked
            } else {
                chkbox_select_all.checked = true;
                if('indeterminate' in chkbox_select_all){
                    chkbox_select_all.indeterminate = true;
                }
            }
        }

        $('.select2').select2();
        $.fn.dataTable.ext.errMode = 'none';
        $(function () {

            var datatable = $("#dataTables-reports").DataTable({
//                fixedHeader: true,
                searching: false,
                serverSide: true,
                processing: true,
                scrollY:  '300px',
                fixedHeader: {
                    header: true,
                    footer: true
                },
                paging: false,
//                scrollY: 300,
//                scroller: {
//                    loadingIndicator: true
//                },
                ajax: {
                    url: '{!! route('reports.dataTables') !!}',
                    data: function (d) {
                        d.date = $('input[name=date]').val();
                        d.user_id = $('select[name=user_id]').val();
                        d.type = $('select[name=type]').val();
                        d.department_id = $('select[name=department_id]').val();
                    }
                },
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                    {data: 'date', name: 'date'},
                    {data: 'social_id', name: 'social_id'},
                    {data: 'social_name', name: 'social_name'},
                    {data: 'social_level', name: 'social_level'},
                    {data: 'social_type', name: 'social_type'},
                    {data: 'result', name: 'result'},
                    {data: 'cost_per_result', name: 'impressions'},
                    {data: 'spend', name: 'spend'}
                ],
                order: [[1, 'desc']],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // converting to interger to find total
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };

                    // computing column Total of the complete result
                    resultTotal = api
                        .column( 6 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    cprTotal = api
                        .column( 7 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    spendTotal = api
                        .column( 8 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer by showing the total with the reference of the column index
                    $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 6 ).footer() ).html($.number(resultTotal));
                    $( api.column( 7 ).footer() ).html($.number(cprTotal));
                    $( api.column( 8 ).footer() ).html($.number(spendTotal));

//                    $('#result_total').html($.number(resultTotal));
//                    $('#cpr_total').html($.number(cprTotal));
//                    $('#spent_total').html($.number(spendTotal));
                }
            });

            // Handle click on checkbox
            $('#dataTables-reports tbody').on('click', 'input[type="checkbox"]', function(e){
                var $row = $(this).closest('tr');

                // Get row data
                var data = datatable.row($row).data();

                console.log(data);

                // Get row ID
                var rowId = data.id;

                // Determine whether row ID is in the list of selected row IDs
                var index = $.inArray(rowId, rows_selected);

                // If checkbox is checked and row ID is not in list of selected row IDs
                if(this.checked && index === -1){
                    rows_selected.push(rowId);
                    updateTotalNumber(1, data);
                    // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                } else if (!this.checked && index !== -1){
                    rows_selected.splice(index, 1);
                    updateTotalNumber(2, data);
                }

                if(this.checked){
                    $row.addClass('success');
                } else {
                    $row.removeClass('success');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(datatable);

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            $('#search-form').on('submit', function(e) {
                var val = $("button[type=submit][clicked=true]").val();
                console.log(val);

                datatable.draw();
                e.preventDefault();
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.input-daterange-datepicker').daterangepicker({
            autoUpdateInput: false,
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-default',
            cancelClass: 'btn-white',
            separator: ' to ',
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        });

        $('.input-daterange-datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        $('.input-daterange-datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('#export-form').on('submit', function (e) {
            $('input[name=filter_department_id]').val($('select[name=department_id]').val());
            $('input[name=filter_user_id]').val($('select[name=user_id]').val());
            $('input[name=filter_date]').val($('input[name=date]').val());
            $('input[name=filter_type]').val($('select[name=type]').val());

            $(this).submit();
            datatable.draw();
            e.preventDefault();
        });

    </script>
@endsection

