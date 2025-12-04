@extends('layouts.app')


@section('content')
    <div class="container">

        @push('background')
            <div class="circle2"></div>
            <div class="circle1"></div>
            <img src="imgs/logo-desert.svg" class="myBg">
        @endpush
        <div class="col-12 offset-1 align-self-center p-2 rounded rounded-lg-0 mt-3 ml-0">
            <h2 class="d-flex d-sm-flex justify-content-center text-info font-weight-light mb-2"><img
                    class="d-flex justify-content-sm-center" src="imgs/logo-desert.svg" style="height: 200px;"></h2>
            <h2 class="text-center text-white">الفارس الملثم</h2>

            <div class="row mt-5 ml-3">
                <div class="col">
                    <a href="{{route('login')}}" class="btn btn-outline-info">ادخل</a>
                </div>
                <div class="col">
                    <a href="{{route('register')}}" class="btn btn-outline-info">سجل</a>

                </div>
            </div>
        </div>
    </div>
@endsection
