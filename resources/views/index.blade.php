@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">HỆ THỐNG CUSTOMER SERVICES</h4>
            <p class="text-muted page-title-alt">Chào mừng bạn {{Sentinel::getUser()->name}}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-success pull-left">
                    <i class="md md-input text-success"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">10</b></h3>
                    <p class="text-muted">Danh sách ticket</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
                <div class="bg-icon bg-icon-pink pull-left">
                    <i class="md md-import-export text-pink"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">10</b></h3>
                    <p class="text-muted">Danh sách ticket đã hoàn thành</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
                <div class="bg-icon bg-icon-purple pull-left">
                    <i class="md md-store text-purple"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">10</b></h3>
                    <p class="text-muted">Số lượng kho bảo hành toàn quốc</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
                <div class="bg-icon bg-icon-info pull-left">
                    <i class="md md-account-child text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">10</b></h3>
                    <p class="text-muted">Số lượng người dùng</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="portlet">
                <div class="portlet-heading">
                    <h3 class="portlet-title text-dark">Thông báo của hệ thống</h3>
                    <div class="portlet-widgets">
                        <a href="javascript:;" data-toggle="reload"><i class="ion-refresh"></i></a>
                        <span class="divider"></span>
                        <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group">
                        @include('flash-message::default')
                    </div>

                    @if ($needGenerateUrl)
                        @foreach ($needGenerateUrl as $key=>$url)
                            <div class="cat-box-small">
                                <a href="{{$url}}">{{ ($key == 'create') ? 'Thêm tài khoản Facebook' : 'Làm mới lại FB Token cho tài khoản <b>'.$url.'</b>' }}</a>
                            </div>
                        @endforeach
                    @endif


                    @if ($user->accounts->count() > 0)
                        <div class="card-box table-responsive">
                            <h4 class="m-t-0 header-title"><b>Danh sách Tài khoản Facebook sử dụng cho API</b></h4>
                            <p class="text-muted font-13 m-b-30"></p>
                            <table class="table table-striped table-bordered table-actions-bar">
                                <thead>
                                <tr>
                                    <th>FacebookID</th>
                                    <th>FacebookName</th>
                                    <th>Ngày Hết Hạn Token</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($user->accounts as $account)
                                    <tr>
                                        <td>{{$account->social_id}}</td>
                                        <td>{{$account->social_name}}</td>
                                        <td>{{$account->api_token_start_date->addDays(60)->toDateString()}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <!-- End row -->
@endsection

@section('scripts')
    <script src="/vendor/ubold/assets/js/jquery.min.js"></script>
    <script src="/vendor/ubold/assets/js/bootstrap.min.js"></script>
    <script src="/vendor/ubold/assets/js/detect.js"></script>
    <script src="/vendor/ubold/assets/js/fastclick.js"></script>
    <script src="/vendor/ubold/assets/js/jquery.slimscroll.js"></script>
    <script src="/vendor/ubold/assets/js/jquery.blockUI.js"></script>
    <script src="/vendor/ubold/assets/js/waves.js"></script>
    <script src="/vendor/ubold/assets/js/wow.min.js"></script>
    <script src="/vendor/ubold/assets/js/jquery.nicescroll.js"></script>
    <script src="/vendor/ubold/assets/js/jquery.scrollTo.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="/vendor/ubold/assets/plugins/c3/c3.min.js"></script>
    {{--<script src="/vendor/ubold/assets/pages/jquery.c3-chart.init.js"></script>--}}

    <!-- App core js -->
    <script src="/vendor/ubold/assets/js/jquery.core.js"></script>
    <script src="/vendor/ubold/assets/js/jquery.app.js"></script>
@endsection