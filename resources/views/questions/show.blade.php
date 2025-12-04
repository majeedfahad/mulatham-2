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
                                <tr>
                                    <td colspan="4">
                                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-3">
                                            <div>
                                                <div class="text-muted small mb-1">نص السؤال</div>
                                                <h5 class="mb-0">{!! nl2br(e($question->title)) !!}</h5>
                                            </div>
                                            @if($question->image_path)
                                                <div class="mt-3 mt-md-0">
                                                    <img src="{{ asset('storage/'.$question->image_path) }}" alt="صورة السؤال" class="img-fluid rounded" style="max-height: 180px;">
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
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
