@extends('layouts.app')

@section('content')
    <div class="container">

        @push('background')
            <div class="circle2"></div>
            <div class="circle1"></div>
            <img src="imgs/logo-desert.png" class="myBg">
        @endpush
        <div class="col-12 p-0 rounded rounded-lg-0 my-2" id="myTable">
            <div id="table_header" class="row text-center d-flex align-content-center" dir="rtl">
                <div class="col-12">الفارس</div>
            </div>

            @foreach ($users->shuffle() as $user)
                <div id="table_row" class="row text-center d-flex align-content-center" dir="rtl">
                    <div class="col-12">{{$user->name}}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
