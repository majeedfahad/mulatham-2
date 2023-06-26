@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h2>إعدادات المسابقة</h2>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">الإعداد</th>
                                    <th scope="col">الأمر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($settings as $setting)
                                    <tr>
                                        <td>{{$setting->label}}</td>
                                        @if($setting->value == 1)
                                            <td><a href="{{route('settings.deActiveSetting', ['id' => $setting->id])}}" class="btn btn-success">إغلاق</a></td>
                                        @else
                                            <td><a href="{{route('settings.activeSetting', ['id' => $setting->id])}}" class="btn btn-warning">فتح</a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    
@endsection