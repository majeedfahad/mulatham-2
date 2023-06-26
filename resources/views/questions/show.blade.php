@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <h2>الأسئلة</h2>
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
                                    <th scope="col">الجواب</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($question->answersUser as $answer)
                                    <tr>
                                        <td>{{$answer->id}}</td>
                                        <td><span>{{$answer->answer}}</span></td>
                                        @if(!$answer->hasAnswered())
                                            <td><a href="{{route('settings.questions.correctAnswer', ['id' => $answer->id])}}" class="btn btn-success">صحيحة</a></td>
                                            <td><a href="{{route('settings.questions.wrongAnswer', ['id' => $answer->id])}}" class="btn btn-warning">خاطئة</a></td>
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