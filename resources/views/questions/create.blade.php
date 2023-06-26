@extends('layouts.app')

@section('content')
    <div class="container text-right">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h1>إنشاء سؤال</h1>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{route('settings.questions.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <h2>اكتب السؤال</h2>
                                <textarea name="question" class="form-control" id="" cols="30" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <h2>نوع الإجابة</h2>
                                <select name="answer_option" id="answer_option" class="form-control" onchange="showChoices(this)">
                                    <option value="1">نصي</option>
                                    <option value="2">خيارات</option>
                                </select>
                            </div>
                            <div class="form-group multiple-choice disabled" id="choices-div">
                                <div class="row p-1">
                                    <div class="col-md-6 col-xs-12 mt-4">
                                        <label for="1">الخيار الأول</label>
                                        <input type="text" name="1" id="" class="form-control">
                                        <input type="radio" name="correct_answer" value="1" id="" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-xs-12 mt-4">
                                        <label for="2">الخيار الثاني</label>
                                        <input type="text" name="2" id="" class="form-control">
                                        <input type="radio" name="correct_answer" value="2" id="" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-xs-12 mt-4">
                                        <label for="3">الخيار الثالث</label>
                                        <input type="text" name="3" id="" class="form-control">
                                        <input type="radio" name="correct_answer" value="3" id="" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-xs-12 mt-4">
                                        <label for="4">الخيار الرابع</label>
                                        <input type="text" name="4" id="" class="form-control">
                                        <input type="radio" name="correct_answer" value="4" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <h2>الدرجة</h2>
                                <input type="number" name="score" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="form-control btn btn-primary">إنشاء</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function showChoices(evt) {
                if(evt.value == 2) {
                    $('#choices-div').removeClass('disabled');
                } else {
                    $('#choices-div').addClass('disabled');
                }
            }
        </script>
    @endpush
@endsection