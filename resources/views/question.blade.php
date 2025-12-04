@extends('layouts.app')


@section('content')
    <div class="container">

        @push('background')
            <div class="circle2"></div>
            <div class="circle1"></div>
            <img src="{{asset('imgs/logo-desert.png')}}" class="myBg">
        @endpush

        <div class="col-12 offset-1 align-self-center p-2 rounded rounded-lg-0 mt-5 ml-0">
            <form action="{{route('answerQuestion', ['id' => $question->id])}}" method="POST">
                @csrf
                <div class="text-center text-white mb-3" style="font-size: 2rem">
                    <span>
                        نقاط السؤال: {{$question->score}}
                    </span>
                </div>
                <div class="text-center">
                    <h3 class="question">{!! nl2br(e($question->title)) !!}</h3>
                </div>
                @if ($question->image_path)
                    <div class="text-center mt-3">
                        <img src="{{ asset('storage/'.$question->image_path) }}" alt="صورة السؤال" class="img-fluid rounded shadow-sm" style="max-height: 280px;">
                    </div>
                @endif

                @if ($question->isText())
                <div class="form-group">
                      {{-- <input type="text" name="answer" id="" > --}}
                      <textarea name="answer" class="border rounded border-info shadow form-control mt-5" placeholder="امممم ... مدري وش اكتب بس حط هنا جوابك"
                      style="height: 3.5rem;" autocomplete="off" cols="30" rows="6"></textarea>
                    </div>
                @else
                <div class="container">

                    @foreach ($question->answers as $answer)
                    <div class="row mt-4">
                        <button class="col btn btn-outline-info text-white" type="button" id="{{$answer->id}}" onclick=" getAnswer(this)">{{$answer->title}}</button>
                    </div>                        
                    @endforeach
                </div>
                <input type="hidden" name="selectedAnswer" value="0" id="answer">
                @endif
                <div class="mt-5">
                <input type="submit" value="إرسال" class="btn btn-outline-warning col mt-5">
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            var selectedAnswer = 0;
            function getAnswer(evt) {
                selectedAnswer = evt.id
                $('#answer').val(selectedAnswer);
                console.log($('#answer').val());
            }

        </script>
    @endpush
@endsection
