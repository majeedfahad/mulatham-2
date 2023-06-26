@if(!Auth::user()->isAdmin() && $question && Auth::user()->isEligibleToAnswer($question))
        <div class="col-md-12  alert alert-warning text-center">
            فيه سؤال ينتظر اجابتك! ادخل عليه <a href="{{route('question', ['id' => $question->id])}}">من هنا</a>
        </div>
@endif

@if(Session::has('success'))
        <div class="col-md-12 alert alert-success text-center">
            {{Session::get('success')}}
        </div>
@endif

@if(Session::has('failed'))
        <div class="col-md-12  alert alert-danger text-center">
            {{Session::get('failed')}}
        </div>
@endif