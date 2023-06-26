@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h1>الاعدادات</h1>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{route('settings.questions.index')}}" class="btn btn-outline-primary btn-lg" style="width: 100%">الأسئلة</a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{route('settings.users')}}" class="btn btn-outline-primary btn-lg" style="width: 100%">المتسابقين</a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{route('settings.admin')}}" class="btn btn-outline-primary btn-lg" style="width: 100%">إعدادات المسابقة</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
