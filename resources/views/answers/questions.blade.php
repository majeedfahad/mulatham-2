@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h2>الأسئلة</h2>
                            </div>
                            <div class="col-md-6" style="text-align: end;">
                                <a class="btn btn-primary" href="{{route('settings.questions.create')}}">إنشاء سؤال</a>
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
                                    <th scope="col">#</th>
                                    <th scope="col">السؤال</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                    <tr>
                                        <td>{{$question->id}}</td>
                                        <td>{{$question->title}}</td>
                                        <td><a href="{{route('settings.questions.edit', ['id' => $question->id])}}" class="btn btn-warning">تعديل</a></td>
                                        <td><a href="{{route('settings.questions.destroy', ['id' => $question->id])}}" class="btn btn-danger">حذف</a></td>
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