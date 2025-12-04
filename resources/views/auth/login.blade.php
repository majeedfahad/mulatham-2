@extends('layouts.app')

@section('content')
    <div class="">
        @push('background')
            <div class="circle2"></div>
            <div class="circle1"></div>
            <img src="imgs/logo-desert.png" class="myBg">
        @endpush
        <div
            class="col-12 align-self-center p-5 rounded rounded-lg-0 mt-5">
            <h2 class="d-flex d-sm-flex justify-content-center text-info font-weight-light mb-2">
                <img class="d-flex justify-content-sm-center" src="imgs/logo-desert.png" style="height: 200px;">
            </h2>
            <form name="login" method="POST" action="{{ route('login') }}" dir="rtl">
                @csrf
                <div class="form-group">
                    <label for="name" class="d-flex text-dark font-weight-bold">الاسم الثنائي</label>
                    <input id="name" type="text"
                        class="border rounded border-info shadow form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}" required autocomplete="off" autofocus>

                    @error('name')
                        <span class="invalid-feedback text-right" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="d-flex text-dark font-weight-bold">كلمة المرور</label>
                    <input id="password" type="password"
                        class="border rounded border-info shadow form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button class="btn btn-info text-left mt-2" type="submit" style="background: #476D7C;">حياك</button>
            </form>
        </div>
    </div>

    @push('styles')
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <style>
            form label {
                color: #4b3a2c;
            }
        </style>
    @endpush
@endsection
