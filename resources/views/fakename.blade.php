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
            <form name="login" method="POST" action="{{ route('fakename.store') }}" dir="rtl">
                @csrf
                <div class="form-group">
                    <label for="fakename" class="d-flex  text-white">اللقب</label>
                    <input id="fakename" type="text"
                           class="border rounded border-info shadow form-control @error('fakename') is-invalid @enderror"
                           name="fakename" value="{{ old('fakename') }}" required autocomplete="off" autofocus
                           data-toggle="tooltip" data-placement="top" title="سري! لا يدري عنه أحد">

                    @error('fakename')
                    <span class="invalid-feedback text-right" role="alert">
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
    @endpush
@endsection
