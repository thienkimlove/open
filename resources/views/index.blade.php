@extends('layouts.app')

@section('styles')
    <link href="/vendor/ubold/assets/plugins/c3/c3.min.css" rel="stylesheet" type="text/css"  />
@endsection

@section('content')
    @php
        $currentUser = Sentinel::getUser();
    @endphp
    <div class="row">
        <div class="col-sm-12">
            {{--<h4 class="page-title">HỆ THỐNG CUSTOMER SERVICES</h4>--}}
            <p class="text-muted page-title-alt">Welcome {{ $currentUser->name }}</p>
        </div>

    </div>

    @if ($currentUser->isAdmin())
        @include('dashboard._admin')
    @elseif ($currentUser->isManager())
        @include('dashboard._manager')
    @else
        @include('dashboard._staff')
    @endif




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