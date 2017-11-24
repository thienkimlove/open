<h4>Ngày {{ \Carbon\Carbon::now()->format('d-m-Y') }}</h4>
<div class="row">
    <div class="col-md-6 col-lg-4">
        <div class="widget-bg-color-icon card-box fadeInDown animated">
            <div class="bg-icon bg-icon-success pull-left">
                <i class="fa fa-money text-success"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark"><b class="counter">{{ number_format($data['total_money']) }} </b></h3>
                <p class="text-muted">Chi phí quảng cáo (VND)</p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="widget-bg-color-icon card-box">
            <div class="bg-icon bg-icon-pink pull-left">
                <i class="fa fa-info text-pink"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark"><b class="counter">{{ number_format($data['total_result']) }}</b></h3>
                <p class="text-muted">Kết quả</p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="widget-bg-color-icon card-box">
            <div class="bg-icon bg-icon-purple pull-left">
                <i class="md md-import-export text-purple"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark"><b class="counter">{{ number_format($data['rate']) }}</b></h3>
                <p class="text-muted">Chi phí / Kết quả</p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="m-t-0 m-b-30 header-title"><b>Chi phí (7 ngày)</b></h4>

            <div id="chart"></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="m-t-0 m-b-30 header-title"><b>Kết quả (7 ngày)</b></h4>

            <div id="chart2"></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="m-t-0 m-b-30 header-title"><b>Tỉ lệ chi phí / Kết quả (7 ngày)</b></h4>

            <div id="chart3"></div>
        </div>
    </div>
</div>

@push('inlinescripts')
    <script>
        c3.generate({
            bindto: '#chart',
            data: {
                x: 'x',
                columns: [
                    ['x'{!! $chart[0]  !!}],
                    ['Chi phí' {!! $chart[1]  !!}],
{{--                    ['Kết quả' {!! $chart[2]  !!}]--}}
                ],
//                axes: {
//                    'Chi phí': 'y',
//                    'Kết quả': 'y2'
//                },
                labels: {

                },
                types: {
                    'Chi phí': 'line',
                    'Kết quả': 'line'
                    // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
                },
            },
            axis : {
                x : {
                    type : 'timeseries',
                    tick: {
                        format: '%d-%m-%Y',
                        rotate: 15,
                        multiline: false
                    }
                },
                y : {
                    tick: {
                        format: d3.format(",")
//                format: function (d) { return "$" + d; }
                    }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = d3.format(',');
                        return id === 'Chi phí' ? format(value) + ' đ' : format(value);
                    }
//            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });

        c3.generate({
            bindto: '#chart2',
            data: {
                x: 'x',
                columns: [
                    ['x'{!! $chart[0]  !!}],
                    ['Kết quả' {!! $chart[2]  !!}]
                    {{--['Tỉ lệ' {!! $chart[3]  !!}]--}}
                ],
                colors: {
                    'Kết quả': '#f9c851'
                }
            },
            axis : {
                x : {
                    type : 'timeseries',
                    tick: {
                        format: '%d-%m-%Y',
                        rotate: 15,
                        multiline: false
                    }
                },
                y : {
                    tick: {
                        format: d3.format(",")
//                format: function (d) { return "$" + d; }
                    }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = d3.format(',');
                        return format(value) ;
                    }
//            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });

        c3.generate({
            bindto: '#chart3',
            data: {
                x: 'x',
                columns: [
                    ['x'{!! $chart[0]  !!}],
                    {{--['Chi phí' {!! $chart[1]  !!}],--}}
                    ['Tỉ lệ' {!! $chart[3]  !!}]
                ],
                colors: {
                    'Tỉ lệ': '#3ac9d6'
                },
            },
            axis : {
                x : {
                    type : 'timeseries',
                    tick: {
                        format: '%d-%m-%Y',
                        rotate: 15,
                        multiline: false
                    }
                },
                y : {
                    tick: {
                        format: d3.format(",")
//                format: function (d) { return "$" + d; }
                    }
                }
            },
            tooltip: {
                format: {
                    value: function (value, ratio, id) {
                        var format = d3.format(',');
                        return format(value) ;
                    }
//            value: d3.format(',') // apply this format to both y and y2
                }
            }
        });

    </script>
@endpush